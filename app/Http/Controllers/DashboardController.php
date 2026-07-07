<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Billing;
use App\Models\Inventario;
use App\Models\Maintenance;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

/**
 * Controlador del Tablero Principal (Dashboard) del sistema.
 * 
 * Recopila y calcula estadísticas en tiempo real y datos financieros (ingresos mensuales,
 * cantidad de usuarios, inventario crítico, órdenes de mantenimiento activas e historial de actividad reciente)
 * basándose en el rol del usuario autenticado, proveyendo los datos a la vista Inertia.
 */
class DashboardController extends Controller
{
    /**
     * Muestra la pantalla principal del Dashboard con estadísticas y gráficos avanzados.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // 1. Total Users
        $totalUsers = User::count();

        // 2. Monthly Revenue
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');

        $monthlyBillingsCount = Billing::whereRaw('MONTH(fecha) = ? AND YEAR(fecha) = ?', [$currentMonth, $currentYear])->count();
        $monthlyRevenue = Billing::whereRaw('MONTH(fecha) = ? AND YEAR(fecha) = ?', [$currentMonth, $currentYear])->sum('total');
        
        $todayRevenue = Billing::whereDate('fecha', today())->sum('total');
        $todayBillingsCount = Billing::whereDate('fecha', today())->count();

        // 3. New Orders / Partidas
        $newPartidasCount = Inventario::whereDate('created_at', today())->count();

        // 4. Critical Stock / Maintenance
        $activeMaintenances = Maintenance::where('status', '!=', 'TERMINADO')->count();
        
        // Maintenances pending billing / conciliation (status RETORNADO)
        $pendingConciliationsCount = Maintenance::where('status', 'RETORNADO')->count();

        // recent activity
        $recentBillings = Billing::with('user')->latest()->take(5)->get()->map(function ($bill) {
            return [
                'id' => $bill->id,
                'description' => "Factura #{$bill->id} generada por {$bill->client_name}",
                'time' => $bill->fecha . ' ' . $bill->hora,
                'color' => 'green',
            ];
        });

        // --- Advanced Stats and Charts tailored per role ---
        $chartData = null;
        $user = auth()->user();
        
        $isBilling = stripos($user->rol, 'fact') !== false || $user->hasAnyRole(['Facturacion', 'Facturación']);
        $isMechanic = stripos($user->rol, 'mecan') !== false || stripos($user->rol, 'tecn') !== false || stripos($user->rol, 'tall') !== false;
        $isInventory = stripos($user->rol, 'inv') !== false || $user->hasAnyRole(['Inventario', 'GESTOR DE INVENTARIO']);
        $isAdminOrSuperUser = stripos($user->rol, 'admin') !== false || stripos($user->rol, 'super') !== false 
            || $user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR']);

        // 1. Revenue Last 6 Months (Shared by Billing and Admins)
        $sixMonthsData = Billing::selectRaw('DATE_FORMAT(fecha, "%Y-%m") as month, SUM(total) as revenue')
            ->where('fecha', '>=', now()->subMonths(6)->format('Y-m-d'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $revenueLabels = $sixMonthsData->pluck('month')->map(function ($m) {
            return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('F');
        });
        $revenueValues = $sixMonthsData->pluck('revenue');

        if ($isBilling) {
            // Invoicing Role: USD vs BS breakdown + Revenue Chart
            $usdCount = Billing::where('divisa', '>', 0)->count();
            $bsCount = Billing::where('bs', '>', 0)->where('divisa', 0)->count();
            
            $chartData = [
                'revenue' => [
                    'labels' => $revenueLabels,
                    'data' => $revenueValues,
                ],
                'inventory' => [
                    'labels' => ['Transacciones USD', 'Transacciones Bolívares'],
                    'data' => [$usdCount, $bsCount],
                    'title' => 'Distribución por Divisa/Moneda'
                ]
            ];
        } elseif ($isMechanic) {
            // Mechanics Role: Maintenance types + states (unlocked operational dashboard)
            $typesData = Maintenance::selectRaw('tipo, count(*) as count')->groupBy('tipo')->get();
            $statesData = Maintenance::selectRaw('status, count(*) as count')->groupBy('status')->get();
            
            $chartData = [
                'revenue' => [
                    'labels' => $typesData->pluck('tipo'),
                    'data' => $typesData->pluck('count'),
                    'title' => 'Mantenimientos por Tipo'
                ],
                'inventory' => [
                    'labels' => $statesData->pluck('status'),
                    'data' => $statesData->pluck('count'),
                    'title' => 'Mantenimientos por Estado'
                ]
            ];
        } elseif ($isInventory) {
            // Inventory Role: Stock types + stock statuses
            $typesData = Inventario::selectRaw('tipo, count(*) as count')->groupBy('tipo')->get();
            $statusesData = Inventario::selectRaw('status, count(*) as count')->groupBy('status')->get();
            
            $chartData = [
                'revenue' => [
                    'labels' => $typesData->pluck('tipo'),
                    'data' => $typesData->pluck('count'),
                    'title' => 'Inventario por Tipo'
                ],
                'inventory' => [
                    'labels' => $statusesData->pluck('status'),
                    'data' => $statusesData->pluck('count'),
                    'title' => 'Distribución por Estado'
                ]
            ];
        } else {
            // Admin / Superuser / General: Inventory type breakdown + Revenue Chart
            $inventoryData = Inventario::selectRaw('tipo, count(*) as count')
                ->where('status', 'DISPONIBLE')
                ->groupBy('tipo')
                ->get();

            $groupedInventory = [
                'Motores' => 0,
                'Cajas' => 0,
                'Cámaras' => 0,
                'Autopartes' => 0,
                'Otros' => 0
            ];

            foreach ($inventoryData as $item) {
                $t = strtoupper($item->tipo);
                if (str_contains($t, 'MOTOR'))
                    $groupedInventory['Motores'] += $item->count;
                elseif (str_contains($t, 'CAJA'))
                    $groupedInventory['Cajas'] += $item->count;
                elseif (str_contains($t, 'CÁMARA'))
                    $groupedInventory['Cámaras'] += $item->count;
                elseif (str_contains($t, 'AUTOPARTE'))
                    $groupedInventory['Autopartes'] += $item->count;
                else
                    $groupedInventory['Otros'] += $item->count;
            }

            $chartData = [
                'revenue' => [
                    'labels' => $revenueLabels,
                    'data' => $revenueValues,
                ],
                'inventory' => [
                    'labels' => array_keys($groupedInventory),
                    'data' => array_values($groupedInventory),
                    'title' => 'Distribución de Inventario'
                ]
            ];
        }
        
        // Count specific metrics to pass
        $completedMaintenancesCount = Maintenance::where('status', 'TERMINADO')->count();
        $totalInventoryCount = Inventario::count();
        $availableInventoryCount = Inventario::where('status', 'DISPONIBLE')->count();
        $inMaintenanceInventoryCount = Inventario::where('status', 'EN MANTENIMIENTO')->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'monthlyRevenue' => number_format($monthlyRevenue, 2),
                'monthlyBillingsCount' => $monthlyBillingsCount,
                'todayRevenue' => number_format($todayRevenue, 2),
                'todayBillingsCount' => $todayBillingsCount,
                'newPartidas' => $newPartidasCount,
                'activeMaintenances' => $activeMaintenances,
                'pendingConciliationsCount' => $pendingConciliationsCount,
                'completedMaintenancesCount' => $completedMaintenancesCount,
                'totalInventoryCount' => $totalInventoryCount,
                'availableInventoryCount' => $availableInventoryCount,
                'inMaintenanceInventoryCount' => $inMaintenanceInventoryCount,
            ],
            'recentActivity' => $recentBillings,
            'charts' => $chartData,
            'utilityPercentage' => (float) Setting::get('utility_percentage', 30),
        ]);
    }

    /**
     * Actualiza el porcentaje de utilidad global y recalcula todos los precios.
     */
    public function updateUtility(Request $request)
    {
        $request->validate([
            'utility_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $user = auth()->user();
        $isBilling = stripos($user->rol, 'fact') !== false || $user->hasAnyRole(['Facturacion', 'Facturación']);

        if (!$isBilling) {
            return redirect()->back()->with('error', 'No autorizado para cambiar la utilidad.');
        }

        Setting::set('utility_percentage', $request->utility_percentage);

        // Recalculate prices for all items in the inventory
        $items = Inventario::all();
        foreach ($items as $item) {
            $item->recalculatePrice();
        }

        return redirect()->back()->with('success', 'Porcentaje de utilidad actualizado y precios recalculados.');
    }
}
