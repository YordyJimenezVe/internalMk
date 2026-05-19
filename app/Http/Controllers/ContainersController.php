<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;

class ContainersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $containers = Container::query()
            ->when($search, function ($query, $search) {
                // Adjust columns to search according to db structure
                $query->where('expediente', 'like', "%{$search}%")
                    ->orWhere('cod', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return inertia('Container/Index', [
            'containers' => $containers,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Container/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $container = new Container();
        $container->fill($request->all());
        $container->save();

        $this->distributeCosts($container);

        return redirect()->route('container');
    }

    /**
     * Display the specified resource.
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
            if ($latestMaintenance && $latestMaintenance->status !== 'FINALIZADO') {
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
     * Show the form for editing the specified resource.
     */
    public function edit(Container $partida, $id)
    {
        $data = Container::findOrFail($id);
        return inertia('Container/Edit', [
            'container' => $data,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $container = Container::findOrFail($id);
        $container->fill($request->all());
        $container->save();

        $this->distributeCosts($container);

        return redirect()->route('container');
    }

    private function distributeCosts(Container $container)
    {
        $items = \App\Models\Inventario::where('container_id', $container->id)->get();
        $itemCount = $items->count();

        if ($container->aplicar_costos && $itemCount > 0) {
            $totalExtraCost = (float) $container->costo_importacion_general;
            $costPerItem = $totalExtraCost / $itemCount;

            foreach ($items as $item) {
                $item->update(['costo_importacion_unitario' => $costPerItem]);
            }
        } else {
            // Reset if disabled or no items
            foreach ($items as $item) {
                $item->update(['costo_importacion_unitario' => 0]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $container = Container::findOrFail($id);
        $container->delete();
        return redirect()->route('container');
    }
}
