<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Inventario;
use App\Models\MaintenanceBill;
use App\Models\Employee;
use Inertia\Inertia;
use App\Models\AccesorioEngine;
use App\Models\Material;

class MaintenancesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        $status = $request->input('status');

        $maintenances = Maintenance::with('partida')
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_mecanico', 'like', "%{$search}%")
                        ->orWhere('apellido_mecanico', 'like', "%{$search}%")
                        ->orWhere('tipo', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('partida', function ($q2) use ($search) {
                            $q2->where('marca', 'like', "%{$search}%")
                                ->orWhere('modelo', 'like', "%{$search}%")
                                ->orWhere('tipo', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return inertia('Maintenance/Index', [
            'maintenances' => $maintenances,
            'filters' => $request->only(['search', 'sort', 'direction', 'status']),
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            $partidaId = $request->input('partida');
            if ($partidaId) {
                return redirect()->route('createBilling', $partidaId);
            }
            return redirect()->route('billing');
        }
        // Mostrar items DISPONIBLES o DEVUELTOS que NO tengan un mantenimiento activo
        $datas = Inventario::whereIn('status', ['DISPONIBLE', 'DEVUELTO'])
            ->whereDoesntHave('maintenances', function ($query) {
                $query->where('status', '!=', 'TERMINADO');
            })->get();

        $partidaId = $request->input('partida');
        $selectedItem = $partidaId ? Inventario::find($partidaId) : $datas->first();

        // Si hay un item seleccionado externamente (ej: por escáner), nos aseguramos de que esté en la lista
        if ($selectedItem && !$datas->contains('id', $selectedItem->id)) {
            $datas->push($selectedItem);
        }

        return inertia('Maintenance/Create', [
            'datas' => $datas,
            'partidas' => $selectedItem,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $maintenance = new Maintenance();
        $maintenance->fill($request->all());
        $maintenance->save();

        return redirect()->route('maintenance');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        $maintenance = Maintenance::with(['partida', 'bills', 'materials', 'accesorios_engine'])->findOrFail($id);

        if ($user->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            return redirect()->route('createBilling', $maintenance->partida_id);
        }

        return inertia('Maintenance/Show', [
            'maintenance' => $maintenance,
            'partida' => $maintenance->partida,
            'bill' => $maintenance->bills->first(),
            'materials' => $maintenance->materials->first(),
            'accesorios' => $maintenance->accesorios_engine->first(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance, $id)
    {
        $maintenance = Maintenance::with('bills')->findOrFail($id);

        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return redirect()->route('maintenance.history')->with('error', 'No tienes permisos para editar un mantenimiento terminado.');
        }

        if (auth()->user()->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !auth()->user()->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            return redirect()->route('createBilling', $maintenance->partida_id);
        }

        $partida = $maintenance->partida;
        $bill = $maintenance->bills->first() ?? (object) [];
        $materials = $maintenance->materials->first() ?? (object) [];
        $accesorios = $maintenance->accesorios_engine->first() ?? (object) [];
        return inertia('Maintenance/Edit', [
            'maintenance' => $maintenance,
            'partida' => $partida,
            'bill' => $bill,
            'materials' => $materials,
            'accesorios' => $accesorios,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $data = array_map(function ($value) {
            return str_replace(' %', '', $value); // Elimina los espacios en blanco y el signo porcentual
        }, $request->all());
        // Separar datos específicos
        $extraDataMaintenance = [
            'maintenances_id' => $id,
            'multi_tools' => $data['multi_tools'] ?? null,
            'mechanic' => $data['mechanic'] ?? 0,
            'mechanic_assistant' => $data['mechanic_assistant'] ?? 0,
            'seller' => $data['seller'] ?? 0,
            'seller_assistant' => $data['seller_assistant'] ?? 0,
            'cleaning' => $data['grouped_commission'] ?? 0,
            'consumables' => $data['grouped_commission'] ?? 0,
            'camera_technician' => $data['camera_technician'] ?? 0,
            'camera_technical_assistant' => $data['camera_technical_assistant'] ?? 0,
            'forklift' => $data['grouped_commission'] ?? 0,
        ];
        // Separar datos específicos para Materiales
        $extraDataMaterials = [
            'maintenances_id' => $id,
            'concha_biela' => $data['concha_biela'] ?? null,
            'concha_bancada' => $data['concha_bancada'] ?? null,
            'anillos' => $data['anillos'] ?? null,
            'empacadura_camara' => $data['empacadura_camara'] ?? null,
            'empacadura_carter' => $data['empacadura_carter'] ?? null,
            'kit_empacaduras' => $data['kit_empacaduras'] ?? null,
            'baño_quimico' => $data['baño_quimico'] ?? null,
            'goma_valvula' => $data['goma_valvula'] ?? null,
            'planos' => $data['planos'] ?? null,
            'valvulas' => $data['valvulas'] ?? null,
            'rectificadora' => $data['rectificadora'] ?? null,
            'asientos' => $data['asientos'] ?? null,
            'camisas' => $data['camisas'] ?? null,
            'levas' => $data['levas'] ?? null,
            'pistones' => $data['pistones'] ?? null
        ];

        // Separar datos específicos para Accesorios (Costos)
        $extraDataAccesorios = [
            'maintenances_id' => $id,
            'valve_cover' => $data['valve_cover'] ?? null,
            'chain_cover' => $data['chain_cover'] ?? null,
            'carter' => $data['carter'] ?? null,
            'pescador' => $data['pescador'] ?? null,
        ];
        $maintenance = Maintenance::findOrFail($id);

        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return redirect()->back()->with('error', 'No tienes permisos para modificar un mantenimiento terminado.');
        }

        // Auto-transition inventory status if maintenance is finished
        if (isset($data['status']) && $data['status'] === 'TERMINADO') {
            $inventario = $maintenance->partida;
            if ($inventario && ($inventario->status === 'DEVUELTO' || $inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA')) {
                $newInvStatus = ($inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA') ? 'VENDIDO' : 'DISPONIBLE';
                $inventario->update(['status' => $newInvStatus]);
            }
        }

        $maintenance->fill($data);
        $maintenance->save();
        // Crear nuevo registro en tabla maintenances_bills
        $otherModel = MaintenanceBill::firstOrCreate(['maintenances_id' => $id]);
        $otherModel->fill($extraDataMaintenance);
        $otherModel->save();

        // Crear nuevo registro en tabla Materials
        $material = Material::firstOrCreate(['maintenances_id' => $id]);
        $material->fill($extraDataMaterials);
        $material->save();

        // Crear/Actualizar registro en tabla AccesorioEngines (Costos)
        $accesorios = AccesorioEngine::firstOrCreate(['maintenances_id' => $id]);
        $accesorios->fill($extraDataAccesorios);
        $accesorios->save();

        // Recalcular costo total y actualizar mantenimiento
        $totalCost = $this->calculateTotalCost($id);
        $maintenance->update(['costo' => $totalCost]);

        return redirect()->route('maintenance');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAccesorios(Request $request, int $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return response()->json(['error' => 'No tienes permisos para modificar un mantenimiento terminado.'], 403);
        }

        $accesorios = AccesorioEngine::firstOrCreate(['maintenances_id' => $id]);
        $accesorios->fill($request->all());
        $accesorios->save();

        // Recalcular costo total
        $totalCost = $this->calculateTotalCost($id);
        $maintenance->update(['costo' => $totalCost]);
    }

    private function calculateTotalCost($maintenanceId)
    {
        $material = Material::where('maintenances_id', $maintenanceId)->first();
        $accesorios = AccesorioEngine::where('maintenances_id', $maintenanceId)->first();

        $total = 0;

        if ($material) {
            $fields = [
                'concha_biela', 'concha_bancada', 'anillos', 'empacadura_camara',
                'empacadura_carter', 'kit_empacaduras', 'baño_quimico', 'goma_valvula',
                'planos', 'valvulas', 'rectificadora', 'asientos', 'camisas', 'levas', 'pistones'
            ];
            foreach ($fields as $field) {
                $value = str_replace(',', '.', $material->$field ?? '0');
                $total += floatval($value);
            }
        }

        if ($accesorios) {
            $fields = ['valve_cover', 'chain_cover', 'carter', 'pescador'];
            foreach ($fields as $field) {
                $value = str_replace(',', '.', $accesorios->$field ?? '0');
                $total += floatval($value);
            }
        }

        return $total;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        if ($maintenance->status === 'TERMINADO' && !auth()->user()->hasAnyRole(['Superusuario', 'SUPERUSUARIO', 'Administrador', 'ADMINISTRADOR'])) {
            return redirect()->back()->with('error', 'No tienes permisos para borrar un mantenimiento terminado.');
        }

        $maintenance->delete();
        return redirect()->route('maintenance');
    }

    public function getInventario(Request $request)
    {
        $inputPartida = $request->input('partida');
        $inputEmployee = $request->input('employee');
        $partida = Inventario::find($inputPartida);
        $employee = Employee::where('cedula', $inputEmployee)->first();
        $datas = Inventario::all();
        return Inertia::render('Maintenance/Create', [
            'partidas' => $partida,
            'employee' => $employee,
            'datas' => $datas,
        ]);
    }

    public function pdf($id)
    {
        $maintenance = Maintenance::with(['partida', 'bills', 'materials', 'accesorios_engine'])->findOrFail($id);

        // --- LOGIC FOR DYNAMIC VEHICLE IMAGE ---
        $brand = strtolower(trim($maintenance->partida->marca ?? ''));
        $rawModel = strtolower(trim($maintenance->partida->modelo ?? ''));

        // Limpiamos nombres compuestos y caracteres especiales
        $cleanModel = str_replace(['/', '-', '(', ')'], ' ', $rawModel);
        $parts = array_filter(explode(' ', $cleanModel));
        $model = reset($parts) ?: '';

        // Filtro de seguridad: Si el modelo es "Turpial" (que es un pájaro), forzamos términos de auto
        $tags = [$brand, $model, 'car'];
        if ($model == 'turpial' || $model == 'festiva') {
            $tags = ['ford', 'festiva', 'car'];
        }

        // Usamos el formato solicitado: marca-modelo
        $query = "{$brand}-{$model}";

        // --- LOGICA PARA MOTORES DIESEL (Cummins, Caterpillar, Perkins, etc) ---
        $engineBrands = ['cummins', 'perkins', 'caterpillar', 'detroit', 'deutz', 'yanmar', 'kubota', 'iveco', 'mack'];
        if (in_array($brand, $engineBrands)) {
            $query = "{$brand}-engine";
        }

        $sourceUrl = "https://loremflickr.com/400/300/{$query}";
        $fallbackUrl = "https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=400";

        try {
            $arrContextOptions = array(
                "ssl" => array("verify_peer" => false, "verify_peer_name" => false),
                "http" => array("timeout" => 5)
            );
            $imageData = @file_get_contents($sourceUrl, false, stream_context_create($arrContextOptions));

            if ($imageData && strlen($imageData) > 2000) {
                $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);
            } else {
                $base64Image = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($fallbackUrl, false, stream_context_create($arrContextOptions)));
            }
        } catch (\Exception $e) {
            $base64Image = null;
        }

        $data = [
            'maintenance' => $maintenance,
            'partida' => $maintenance->partida,
            'bill' => $maintenance->bills->first(),
            'materials' => $maintenance->materials->first(),
            'accesorios' => $maintenance->accesorios_engine->first(),
            'vehicleImage' => $base64Image,
        ];

        $pdf = \PDF::loadView('reports.maintenance_pdf', $data);
        $pdf->setPaper('letter', 'portrait');

        $filename = 'Ficha-Mantenimiento-' . str_pad($maintenance->id, 5, '0', STR_PAD_LEFT) . '.pdf';
        return $pdf->stream($filename);
    }
}
