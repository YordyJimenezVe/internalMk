<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Billing;
use App\Models\Inventario;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Users
        $totalUsers = auth()->user()->hasAnyRole(['Superusuario', 'Administrador']) ? User::count() : 0;

        // 2. Monthly Revenue
        $monthlyRevenue = 0;
        $monthlyBillingsCount = 0;
        if (auth()->user()->hasAnyRole(['Superusuario', 'Administrador', 'Facturacion'])) {
            $currentMonth = now()->format('m');
            $currentYear = now()->format('Y');

            $monthlyBillingsCount = Billing::whereRaw('MONTH(fecha) = ? AND YEAR(fecha) = ?', [$currentMonth, $currentYear])->count();
            $monthlyRevenue = Billing::whereRaw('MONTH(fecha) = ? AND YEAR(fecha) = ?', [$currentMonth, $currentYear])
                ->sum('total');
        }

        // 3. New Orders / Partidas
        $newPartidasCount = Inventario::whereDate('created_at', today())->count();

        // 4. Critical Stock / Maintenance
        $activeMaintenances = Maintenance::where('status', '!=', 'TERMINADO')->count();

        // recent activity
        $recentBillings = collect();
        if (auth()->user()->hasAnyRole(['Superusuario', 'Administrador', 'Facturacion'])) {
            $recentBillings = Billing::with('user')->latest()->take(5)->get()->map(function ($bill) {
                return [
                    'id' => $bill->id,
                    'description' => "Factura #{$bill->id} generada",
                    'time' => $bill->fecha . ' ' . $bill->hora,
                    'color' => 'green',
                ];
            });
        }

        // ... existing basic stats ...

        // --- Advanced Stats for Admin/Superuser ---
        $chartData = null;
        if (auth()->user()->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {

            // 1. Revenue Last 6 Months
            $sixMonthsData = Billing::selectRaw('DATE_FORMAT(fecha, "%Y-%m") as month, SUM(total) as revenue')
                ->where('fecha', '>=', now()->subMonths(6)->format('Y-m-d'))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Fill missing months if necessary or just pass what we have
            $revenueLabels = $sixMonthsData->pluck('month')->map(function ($m) {
                return \Carbon\Carbon::createFromFormat('Y-m', $m)->translatedFormat('F');
            });
            $revenueValues = $sixMonthsData->pluck('revenue');

            // 2. Inventory Breakdown
            $inventoryData = Inventario::selectRaw('tipo, count(*) as count')
                ->where('status', 'DISPONIBLE') // Only available stock?
                ->groupBy('tipo')
                ->get();

            // Group motors together for cleaner chart?
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
                ]
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalUsers' => $totalUsers,
                'monthlyRevenue' => number_format($monthlyRevenue, 2),
                'newPartidas' => $newPartidasCount,
                'activeMaintenances' => $activeMaintenances,
            ],
            'recentActivity' => $recentBillings,
            'charts' => $chartData // Pass to frontend
        ]);
    }
}
