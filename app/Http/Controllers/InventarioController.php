<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Container;
use App\Models\Bills;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchRaw = $request->input('search', '');

        // --- Smart Redirect Logic ---
        // Detect corrupted URL pattern from scanner (US->ES keyboard mismatch)
        // Expected: http://... -> Received: httpÑ--...
        if (str_starts_with($searchRaw, 'http')) {
            // Attempt to restore valid characters
            // 'Ñ' might be ':'
            // '-' might be '/'
            // We just look for the ID at the end to be safe
            if (preg_match('/(\d+)$/', $searchRaw, $matches)) {
                return redirect()->route('showInventario', $matches[1]);
            }
        }

        // --- Barcode Sanitization ---
        // Fix Barcode scanner mapping: ''' (US key ' in ES layout?) -> '-'
        // The user reported "TRHU'616'128" -> "TRHU-616-128"
        $search = str_replace("'", "-", $searchRaw);

        // --- Search Query ---
        // --- Filter Logic ---
        $statusFilter = $request->input('status', 'DISPONIBLE'); // Default: Only Available
        $typeFilter = $request->input('type_filter', null); // New Type Filter

        // REMOVED 'AUTOPARTE' EXCLUSION to consolidate modules
        $inventarios = Inventario::with('container');

        // Apply Type Filter
        if ($typeFilter) {
            if ($typeFilter === 'motores') {
                $inventarios->where('tipo', 'LIKE', '%MOTOR%');
            } elseif ($typeFilter === 'cajas') {
                $inventarios->where('tipo', 'LIKE', '%CAJA%');
            } elseif ($typeFilter === 'camaras') {
                $inventarios->where('tipo', 'LIKE', '%CÁMARA%');
            } elseif ($typeFilter === 'autopartes') {
                $inventarios->where('tipo', 'AUTOPARTE');
            }
        }

        // Apply Status Filter
        if ($statusFilter === 'DISPONIBLE') {
            $inventarios->whereDoesntHave('bill')
                ->whereIn('status', ['DISPONIBLE', 'DEVUELTO']);
        } elseif ($statusFilter === 'VENDIDO') {
            $inventarios->where(function ($q) {
                $q->has('bill')->orWhere('status', 'VENDIDO');
            });
        }
        // If 'ALL', we don't filter by billing/status, just show everything.

        if ($search) {
            // Dividimos la búsqueda en palabras clave separadas por espacios
            $keywords = explode(' ', strtolower($search));

            $marcaKeyword = array_shift($keywords);

            $inventarios->where(function ($query) use ($marcaKeyword, $keywords, $search, $searchRaw) {
                // Expanded Search Logic
                $query->whereRaw('LOWER(marca) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(tipo) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(codInv) LIKE ?', ['%' . $search . '%'])
                    ->orWhereHas('container', function ($q) use ($search) {
                        $q->whereRaw("CONCAT(SUBSTR(cod, 1, 4), '-', codInv) LIKE CONCAT('%', ?, '%')", [
                            $search
                        ]);
                    });

                if ($searchRaw) {
                    // Barcode/Exact match priority
                    $query->orWhere('id', $searchRaw);
                }

                // Original complex logic for keyword matching
                $query->orWhere(function ($subQuery) use ($marcaKeyword, $keywords) {
                    $subQuery->whereRaw('LOWER(marca) LIKE ?', ['%' . $marcaKeyword . '%']);
                    if (!empty($keywords)) {
                        $subQuery->where(function ($modelQuery) use ($keywords) {
                            foreach ($keywords as $keyword) {
                                $modelQuery->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . $keyword . '%']);
                            }
                        });
                    }
                });
            });


        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $inventarios->orderBy($sort, $direction);

        $motorTypes = ['MOTOR 7/8', 'MOTOR 3/4', 'MOTOR COMPLETO', 'MOTOR 5/8'];
        $tipos = Inventario::whereDoesntHave('bill')
            ->selectRaw('
            SUM(CASE WHEN tipo LIKE "%motor%" THEN 1 ELSE 0 END) AS motores,
            SUM(CASE WHEN tipo = "CAJA AUTOMÁTICA" THEN 1 ELSE 0 END) AS cajas_automaticas,
            SUM(CASE WHEN tipo = "AUTOPARTE" THEN 1 ELSE 0 END) AS autopartes,
            SUM(CASE WHEN tipo = "CÁMARA" THEN 1 ELSE 0 END) AS camaras
        ')
            ->orderBy('created_at', 'desc')
            ->get();

        $response = $inventarios->paginate(15)->appends($request->query());

        return inertia('Inventario/Index', [
            'partidas' => $response,
            'Inventarios' => $response,
            'rows' => $response,
            "filters" => [
                'search' => $searchRaw,
                'status' => $statusFilter,
                'type_filter' => $typeFilter,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'tipos' => $tipos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $containers = Container::all();
        return inertia(
            'Inventario/Create',
            [
                'containers' => $containers
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreInventarioRequest $request)
    {
        $inventario = new Inventario();
        $data = $request->all();

        // 1. Auto-generate codInv for IMPORTADO items if not provided
        if ($request->origen === 'IMPORTADO' && empty($request->codInv)) {
            // Extract the numeric part of codInv and find the max
            // Pattern: we look for codes that are either just numbers or contain a dash (like '623-135')
            // If it's '623-135', the '623' is usually the ID-like part or container related.
            // But based on user request "correlativo de la base de datos", 
            // we will find the highest numeric code currently in the system.

            $lastCode = Inventario::whereRaw("codInv REGEXP '^[0-9]+$'")
                ->selectRaw("CAST(codInv AS UNSIGNED) as numeric_code")
                ->orderBy('numeric_code', 'desc')
                ->first();

            $nextNumber = $lastCode ? ($lastCode->numeric_code + 1) : 1;

            // However, looking at data "623-135", we might want to just follow the count or simple ID
            // Let's stick to a simple numeric increment for the base "codInv".
            $data['codInv'] = (string) $nextNumber;
        }

        // 2. Auto-generate item description
        if ($request->tipo === 'AUTOPARTE') {
            $data['item'] = trim(($request->marca ?? '') . ' ' . ($request->modelo ?? '') . ' ' . ($request->categorie ?? ''));
        } else {
            $data['item'] = trim(($request->tipo ?? '') . ' ' . ($request->marca ?? '') . ' ' . ($request->modelo ?? ''));
        }

        $inventario->fill($data);
        $inventario->save();

        return redirect()->route('inventario');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        $isMechanic = $user && $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO', 'Superusuario', 'Administrador']);

        if ($isMechanic) {
            return app(\App\Http\Controllers\ScanController::class)->directToMaintenance($id);
        }

        $data = Inventario::with(['container', 'maintenances', 'bill', 'billingRequests'])->findOrFail($id);

        // QR Code
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = $writer->writeString(route('showInventario', $data->id));

        // Barcode
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $containerCode = substr($data->container->cod, 0, 4);
        $barcodeData = strtoupper($containerCode . '-' . $data->codInv);
        // $barcodeData = $data->codInv && $data->codInv != '0' ? $data->codInv : str_pad($data->id, 8, '0', STR_PAD_LEFT);
        $barcode = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 1);

        return inertia('Inventario/Show', [
            'inventario' => $data,
            'qrCode' => (string) $qrCode,
            'barcode' => (string) $barcode,
            'barcodeData' => $barcodeData,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $data = Inventario::with('container')
            ->whereId($id)
            ->get()->first();
        $containers = Container::all();
        $tipos = ['MOTOR 3/4', 'MOTOR 5/8', 'MOTOR 7/8', 'MOTOR COMPLETO', 'CAJA AUTOMÁTICA', 'CÁMARA', 'AUTOPARTE'];
        return inertia('Inventario/Edit', [
            'inventario' => $data,
            'containers' => $containers,
            'tipos' => $tipos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateInventarioRequest $request, int $id)
    {
        $inventario = Inventario::findOrFail($id);
        $data = $request->validated(); // Use validated data

        // Re-generate item just in case, although prepareForValidation in Request should handle it
        if ($data['tipo'] === 'AUTOPARTE') {
            $data['item'] = trim($data['tipo'] . ' ' . ($data['categorie'] ?? ''));
        } else {
            $data['item'] = trim($data['tipo'] . ' ' . ($data['marca'] ?? '') . ' ' . ($data['modelo'] ?? ''));
        }

        $inventario->update($data);

        return redirect()->route('inventario')->with('success', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $inventario = Inventario::findOrFail($id);
        $inventario->delete();
        return redirect()->route('inventario');
    }
}
