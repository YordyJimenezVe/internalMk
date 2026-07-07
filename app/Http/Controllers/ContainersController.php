<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;

/**
 * Controlador para la gestión de Contenedores de Importación.
 * 
 * Centraliza la administración de los lotes de importación, permitiendo registrar
 * nuevos contenedores, editar sus especificaciones y distribuir de forma prorrateada
 * los costos generales de importación entre todos los artículos asociados.
 */
class ContainersController extends Controller
{
    /**
     * Muestra el listado de contenedores con filtros de búsqueda, ordenación y paginación.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros y dirección de ordenación.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        // Clean barcode scanner mapping (apostrophe to hyphen) immediately
        $searchCleaned = $search ? str_replace("'", "-", $search) : '';
        
        $user = auth()->user();
        $isMechanic = $user && $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO']);

        // --- Smart Redirect Logic (Global) ---
        if ($searchCleaned) {
            $partida = null;
            if (str_contains($searchCleaned, '-') || (strlen($searchCleaned) >= 4 && !is_numeric($searchCleaned))) {
                // 1. Try exact match on codInv
                $partida = \App\Models\Inventario::where('codInv', $searchCleaned)->first();
                
                // 2. Try matching container code and inventory code (e.g. CXDU-15 -> container CXDU, codInv 15)
                if (!$partida && str_contains($searchCleaned, '-')) {
                    $lastDashPos = strrpos($searchCleaned, '-');
                    $containerPart = substr($searchCleaned, 0, $lastDashPos);
                    $codInvPart = substr($searchCleaned, $lastDashPos + 1);
                    
                    $partida = \App\Models\Inventario::where('codInv', $codInvPart)
                        ->whereHas('container', function($q) use ($containerPart) {
                            $q->where('cod', 'LIKE', $containerPart . '%');
                        })->first();
                        
                    if (!$partida) {
                        $partida = \App\Models\Inventario::where('codInv', $codInvPart)->first();
                    }
                }
            }
            
            if ($partida) {
                if ($isMechanic) {
                    return app(\App\Http\Controllers\ScanController::class)->directToMaintenance($partida->id);
                }
                return redirect()->route('showInventario', $partida->id);
            }
        }
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $containers = Container::query()
            ->when($searchCleaned, function ($query, $searchCleaned) {
                $query->where(function ($q) use ($searchCleaned) {
                    $q->where('expediente', 'like', "%{$searchCleaned}%")
                      ->orWhere('cod', 'like', "%{$searchCleaned}%")
                      ->orWhere('fecha', 'like', "%{$searchCleaned}%")
                      ->orWhere('hora', 'like', "%{$searchCleaned}%")
                      ->orWhere('motores', 'like', "%{$searchCleaned}%")
                      ->orWhere('cajas', 'like', "%{$searchCleaned}%")
                      ->orWhere('camaras', 'like', "%{$searchCleaned}%")
                      ->orWhere('accesorios', 'like', "%{$searchCleaned}%")
                      ->orWhereRaw('(motores + cajas + camaras + accesorios) LIKE ?', ["%{$searchCleaned}%"]);

                    // Try to convert DD/MM/YYYY or DD-MM-YYYY to YYYY-MM-DD
                    if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $searchCleaned, $matches)) {
                        $dateSearch = sprintf('%04d-%02d-%02d', $matches[3], $matches[2], $matches[1]);
                        $q->orWhere('fecha', 'like', "%{$dateSearch}%");
                    }
                    // Try to convert MM/YYYY or MM-YYYY to YYYY-MM
                    elseif (preg_match('/^(\d{1,2})[\/\-](\d{4})$/', $searchCleaned, $matches)) {
                        $dateSearch = sprintf('%04d-%02d', $matches[2], $matches[1]);
                        $q->orWhere('fecha', 'like', "%{$dateSearch}%");
                    }
                    // Try to convert DD/MM or DD-MM to -MM-DD
                    elseif (preg_match('/^(\d{1,2})[\/\-](\d{1,2})$/', $searchCleaned, $matches)) {
                        $dateSearch = sprintf('-%02d-%02d', $matches[2], $matches[1]);
                        $q->orWhere('fecha', 'like', "%{$dateSearch}%");
                    }
                });
            })
            ->when($sort === 'total', function ($query) use ($direction) {
                $query->orderByRaw('(motores + cajas + camaras + accesorios) ' . $direction);
            }, function ($query) use ($sort, $direction) {
                $allowed = ['cod', 'expediente', 'fecha', 'hora', 'motores', 'cajas', 'camaras', 'accesorios', 'created_at'];
                $sortBy = in_array($sort, $allowed) ? $sort : 'created_at';
                $query->orderBy($sortBy, $direction);
            })
            ->paginate(10)
            ->withQueryString();

        return inertia('Container/Index', [
            'containers' => $containers,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo contenedor.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return inertia('Container/Create');
    }

    /**
     * Almacena un nuevo contenedor y recalcula el prorrateo de costos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos de creación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $container = new Container();
        $container->fill($request->all());
        $container->save();

        return redirect()->route('container');
    }

    /**
     * Muestra los detalles y estadísticas financieras de un contenedor específico.
     *
     * @param  string  $id  Identificador único del contenedor.
     * @return \Inertia\Response
     */
    public function show(string $id)
    {
        $container = Container::findOrFail($id);

        // Items registered for this container
        $items = \App\Models\Inventario::where('container_id', $id)
            ->with([
                'maintenances' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }
            ])
            ->get();

        $totalExpected = ($container->motores ?? 0) + ($container->cajas ?? 0) + ($container->camaras ?? 0) + ($container->accesorios ?? 0);
        $totalRegistered = $items->count();

        $notLoadedCount = max(0, $totalExpected - $totalRegistered);

        $soldItems = $items->where('status', 'VENDIDO');
        $soldCount = $soldItems->count();

        // An item is in maintenance if it has at least one maintenance record and its not finished
        // We'll check the latest maintenance record status
        $inMaintenanceCount = 0;
        $availableCount = 0;

        foreach ($items as $item) {
            if ($item->status === 'VENDIDO')
                continue;

            $latestMaintenance = $item->maintenances->first();
            if ($latestMaintenance && $latestMaintenance->status !== 'TERMINADO') {
                $inMaintenanceCount++;
            } else {
                if ($item->status === 'DISPONIBLE') {
                    $availableCount++;
                }
            }
        }

        // Financials
        $totalSoldPrice = $soldItems->sum('price_sale');
        $totalCostSold = $soldItems->sum('costo') + $soldItems->sum('costo_importacion_unitario');
        $profit = $totalSoldPrice - $totalCostSold;

        // Percentages (relative to expected total)
        $stats = [
            'total_expected' => $totalExpected,
            'registered' => $totalRegistered,
            'available' => [
                'count' => $availableCount,
                'percentage' => $totalExpected > 0 ? round(($availableCount / $totalExpected) * 100, 2) : 0
            ],
            'maintenance' => [
                'count' => $inMaintenanceCount,
                'percentage' => $totalExpected > 0 ? round(($inMaintenanceCount / $totalExpected) * 100, 2) : 0
            ],
            'sold' => [
                'count' => $soldCount,
                'percentage' => $totalExpected > 0 ? round(($soldCount / $totalExpected) * 100, 2) : 0
            ],
            'not_loaded' => [
                'count' => $notLoadedCount,
                'percentage' => $totalExpected > 0 ? round(($notLoadedCount / $totalExpected) * 100, 2) : 0
            ],
            'financials' => [
                'total_revenue' => $totalSoldPrice,
                'total_profit' => $profit,
                'import_costs' => [
                    'total' => (float) $container->costo_importacion_general,
                    'aplicado' => (bool) $container->aplicar_costos
                ]
            ],
            'categories' => [
                'motores' => $items->filter(fn($item) => str_contains(strtolower($item->tipo), 'motor'))->count(),
                'cajas' => $items->filter(fn($item) => str_contains(strtolower($item->tipo), 'caja'))->count(),
                'camaras' => $items->filter(fn($item) => str_contains(strtolower($item->tipo), 'cámara') || str_contains(strtolower($item->tipo), 'camara'))->count(),
                'autopartes' => $items->where('tipo', 'AUTOPARTE')->count(),
            ]
        ];

        return inertia('Container/Show', [
            'container' => $container,
            'stats' => $stats
        ]);
    }

    /**
     * Muestra el formulario de edición de un contenedor.
     *
     * @param  \App\Models\Container  $partida  Modelo de contenedor (herencia de ruta).
     * @param  string  $id  Identificador único del contenedor.
     * @return \Inertia\Response
     */
    public function edit(Container $partida, $id)
    {
        $data = Container::findOrFail($id);
        return inertia('Container/Edit', [
            'container' => $data,
        ]);
    }

    /**
     * Actualiza el contenedor en la base de datos y recalcula el prorrateo de costos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos modificados.
     * @param  int  $id  Identificador único del contenedor.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $container = Container::findOrFail($id);
        $container->fill($request->all());
        $container->save();

        return redirect()->route('container');
    }



    /**
     * Elimina el contenedor de la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP.
     * @param  int  $id  Identificador único del contenedor a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        $container = Container::findOrFail($id);
        $container->delete();
        return redirect()->route('container');
    }
}
