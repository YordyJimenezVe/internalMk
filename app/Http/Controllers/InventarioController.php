<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Container;
use App\Models\Bills;
use Inertia\Inertia;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchRaw = $request->input('search', '');
        $user = auth()->user();
        $isMechanic = $user && $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO']);

        // --- Smart Redirect Logic (Global) ---
        if ($searchRaw) {
            $partida = null;
            if (str_contains($searchRaw, '-') || (strlen($searchRaw) >= 4 && !is_numeric($searchRaw))) {
                // 1. Try exact match
                $query = Inventario::query();
                if (is_numeric($searchRaw)) {
                    $query->where(function ($q) use ($searchRaw) {
                        $q->where('id', $searchRaw)->orWhere('codInv', $searchRaw);
                    });
                } else {
                    $query->where('codInv', $searchRaw);
                }
                $partida = $query->first();
                
                // 2. Try cleaning prefix if not found (e.g. CRSU-623-135 -> 623-135)
                if (!$partida && str_contains($searchRaw, '-')) {
                    $parts = explode('-', $searchRaw);
                    if (count($parts) >= 2) {
                        $strippedCode = implode('-', array_slice($parts, -2));
                        $partida = Inventario::where('codInv', $strippedCode)->first();
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

        // Final protection: If not a mechanic and doesn't have permission, block access
        if (!$isMechanic && !$user->can('view partida')) {
            abort(403);
        }

        // --- URL/Scanner Redirection (Existing logic) ---
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
        } elseif ($statusFilter === 'GARANTIA') {
            $inventarios->whereIn('status', ['GARANTIA', 'GARANTÍA']);
        } elseif ($statusFilter === 'PRECIO PENDIENTE') {
            $inventarios->where('status', 'PRECIO PENDIENTE');
        } elseif ($statusFilter === 'INOPERATIVO-DESARMADO') {
            $inventarios->where('status', 'INOPERATIVO-DESARMADO');
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
                    ->orWhereRaw('LOWER(serial) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(expediente) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(categorie) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('LOWER(observation) LIKE ?', ['%' . $search . '%'])
                    ->orWhere('año', 'like', "%{$search}%")
                    ->orWhere('cantidad', 'like', "%{$search}%")
                    ->orWhereHas('container', function ($q) use ($search) {
                        $q->whereRaw("CONCAT(SUBSTR(cod, 1, 4), '-', codInv) LIKE CONCAT('%', ?, '%')", [
                            $search
                        ]);
                    });

                if ($searchRaw && is_numeric($searchRaw)) {
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
        
        if ($sort === 'container.cod') {
            $inventarios->select('inventarios.*')
                ->leftJoin('containers', 'inventarios.container_id', '=', 'containers.id')
                ->orderBy('containers.cod', $direction);
        } elseif ($sort === 'model_display') {
            $inventarios->orderBy('marca', $direction)
                ->orderBy('modelo', $direction);
        } else {
            $allowedSorts = ['id', 'codInv', 'expediente', 'tipo', 'serial', 'año', 'categorie', 'cantidad', 'created_at'];
            $sortBy = in_array($sort, $allowedSorts) ? $sort : 'created_at';
            $inventarios->orderBy($sortBy, $direction);
        }

        $motorTypes = ['MOTOR 7/8', 'MOTOR 3/4', 'MOTOR COMPLETO', 'MOTOR 5/8'];
        $tipos = Inventario::whereDoesntHave('bill')
            ->selectRaw('
            SUM(CASE WHEN tipo LIKE "%motor%" THEN 1 ELSE 0 END) AS motores,
            SUM(CASE WHEN tipo LIKE "%caja%" THEN 1 ELSE 0 END) AS cajas_automaticas,
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
        $tipos = ['MOTOR 3/4', 'MOTOR 5/8', 'MOTOR 7/8', 'MOTOR COMPLETO', 'CAJA AUTOMÁTICA', 'CAJA SINCRÓNICA', 'CÁMARA', 'AUTOPARTE'];
        return inertia(
            'Inventario/Create',
            [
                'containers' => $containers,
                'tipos' => $tipos
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
        $isBilling = $user && $user->hasAnyRole(['FACTURACION', 'Facturacion', 'facturacion']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR']);
        $isMechanic = $user && $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO']);

        // if ($isBilling) {
        //     $partida = Inventario::with('bill')->findOrFail($id);
        //     if ($partida->status === 'VENDIDO' || $partida->bill->count() > 0) {
        //         $bill = $partida->bill->last();
        //         if ($bill) {
        //             return redirect()->route('editBilling', $bill->id);
        //         }
        //     }
        //     return redirect()->route('createBilling', $id);
        // }

        if ($isMechanic) {
            return app(\App\Http\Controllers\ScanController::class)->directToMaintenance($id);
        }

        $data = Inventario::with(['container', 'maintenances', 'bill', 'billingRequests'])->findOrFail($id);
        $data->append('costo_taller');
        $data->serial_image_url = $data->serial_image_path ? asset('storage/' . $data->serial_image_path) : null;

        // Barcode Data (Standardized internal code)
        $containerCode = $data->container ? substr($data->container->cod, 0, 4) : 'MK';
        $barcodeData = strtoupper($containerCode . '-' . $data->codInv);

        // QR Code (Now using internal code instead of URL)
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = $writer->writeString($barcodeData);

        // Barcode
        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $barcode = $generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 1);

        $latestRate = \App\Models\ExchangeRate::where('source', 'BCV')->latest()->first();
        $tasaBCV = $latestRate ? (float) $latestRate->rate : 0;

        return inertia('Inventario/Show', [
            'inventario' => $data,
            'qrCode' => (string) $qrCode,
            'barcode' => (string) $barcode,
            'barcodeData' => $barcodeData,
            'tasa_bcv' => $tasaBCV,
        ]);
    }

    public function printLabel($id)
    {
        $this->checkNotReadOnly();
        $data = Inventario::with(['container'])->findOrFail($id);

        // Barcode Data
        $containerCode = $data->container ? substr($data->container->cod, 0, 4) : 'MK';
        $barcodeData = strtoupper($containerCode . '-' . $data->codInv);

        // QR Code
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = base64_encode($writer->writeString($barcodeData)); // Using base64 for embedding in HTML/PDF

        // Barcode
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($barcodeData, $generator::TYPE_CODE_128, 2, 40));

        return view('labels.single', [
            'inventario' => $data,
            'qrCode' => $qrCode,
            'barcode' => $barcode,
            'barcodeData' => $barcodeData,
            'type' => request('type', 'all'),
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
        $tipos = ['MOTOR 3/4', 'MOTOR 5/8', 'MOTOR 7/8', 'MOTOR COMPLETO', 'CAJA AUTOMÁTICA', 'CAJA SINCRÓNICA', 'CÁMARA', 'AUTOPARTE'];
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

        if ($request->hasFile('serial_file')) {
            $serialPath = \App\Helpers\ImageHelper::compressAndStore($request->file('serial_file'), 'serial_captures');
            $data['serial_image_path'] = $serialPath;
        }

        // Re-generate item just in case, although prepareForValidation in Request should handle it
        if ($data['tipo'] === 'AUTOPARTE') {
            $data['item'] = trim($data['tipo'] . ' ' . ($data['categorie'] ?? ''));
        } else {
            $data['item'] = trim($data['tipo'] . ' ' . ($data['marca'] ?? '') . ' ' . ($data['modelo'] ?? ''));
        }

        $inventario->update($data);

        // Auto-create maintenance ticket if status is GARANTIA/GARANTÍA and no active maintenance exists
        if ($inventario->status === 'GARANTIA' || $inventario->status === 'GARANTÍA') {
            $hasActiveMaintenance = \App\Models\Maintenance::where('partida_id', $inventario->id)
                ->whereIn('status', ['EN ESPERA', 'EN PROCESO'])
                ->exists();
            if (!$hasActiveMaintenance) {
                $billing = \App\Models\Billing::where('partida_id', $inventario->id)->where('status', '!=', 'ANULADA')->first();
                $newMaint = \App\Models\Maintenance::create([
                    'fecha' => now()->format('Y-m-d'),
                    'descripcion' => 'DEVOLUCIÓN TEMPORAL POR GARANTÍA. FACTURA ORIGINAL: #' . ($billing->numero_factura ?? 'S/N'),
                    'tipo' => 'GARANTÍA',
                    'status' => 'EN ESPERA',
                    'partida_id' => $inventario->id,
                    'cedula_mecanico' => 0,
                    'nombre_mecanico' => 'POR',
                    'apellido_mecanico' => 'ASIGNAR',
                    'observaciones' => 'Creado automáticamente tras actualización de estatus a Garantía/Devolución Temporal.',
                ]);

                // Notify via Telegram Group
                $itemName = "{$inventario->marca} {$inventario->modelo}";
                $telegramMessage = "🔧 <b>Nuevo Ingreso a Taller (Mantenimiento por Garantía)</b>\n\n"
                    . "📦 <b>Motor:</b> {$itemName}\n"
                    . "⚙️ <b>Tipo:</b> GARANTÍA\n"
                    . "👤 <b>Mecánico:</b> ⚠️ POR ASIGNAR\n"
                    . "📋 <b>Estatus:</b> RECIBIDO\n"
                    . "📝 <b>Descripción:</b> DEVOLUCIÓN TEMPORAL POR GARANTÍA. FACTURA ORIGINAL: #" . ($billing->numero_factura ?? 'S/N') . "\n\n"
                    . "🔗 <a href=\"" . route('maintenance') . "\">Ver bandeja de taller</a>";
                \App\Services\TelegramService::sendMessage($telegramMessage);
            }
        }

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

    /**
     * Display the thermal label generator dashboard.
     */
    public function generatorDashboard()
    {
        $this->checkNotReadOnly();
        $containers = Container::orderBy('cod', 'asc')->get();
        $brands = Inventario::whereNotNull('marca')
            ->where('marca', '!=', '')
            ->select('marca')
            ->distinct()
            ->orderBy('marca', 'asc')
            ->pluck('marca');

        return view('labels.generator', [
            'containers' => $containers,
            'brands' => $brands,
        ]);
    }

    /**
     * Genera un pliego masivo de etiquetas en formato Carta, filtradas por contenedor, tipo de parte y marca.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function printContainerLabels(Request $request)
    {
        $this->checkNotReadOnly();
        $request->validate([
            'container_id' => 'required|string',
            'type' => 'nullable|string',
            'brand' => 'nullable|string',
            'cost_filter' => 'nullable|string|in:all,without_cost,with_cost',
        ]);

        $costFilter = $request->input('cost_filter', 'all');

        $query = Inventario::with('container');

        if ($costFilter === 'without_cost') {
            $query->where(function ($q) {
                $q->whereNull('costo_importacion_unitario')
                  ->orWhere('costo_importacion_unitario', 0);
            })->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->where('tipo', 'NOT LIKE', '%AUTOPARTE%')
                          ->where('tipo', 'NOT LIKE', '%autoparte%');
                })->orWhere('status', 'PRECIO PENDIENTE');
            });
        } elseif ($costFilter === 'with_cost') {
            $query->whereIn('status', ['DISPONIBLE', 'DEVUELTO'])
                  ->where('costo_importacion_unitario', '>', 0);
        } else {
            $query->whereIn('status', ['DISPONIBLE', 'DEVUELTO']);
        }

        // Filtrado por Contenedor
        if ($request->container_id !== 'all') {
            $request->validate([
                'container_id' => 'exists:containers,id',
            ]);
            $query->where('container_id', $request->container_id);
        }

        // Filtrado por Tipo
        if ($request->filled('type') && $request->type !== 'all') {
            $type = $request->type;
            if ($type === 'motors') {
                $query->where('tipo', 'LIKE', '%MOTOR%');
            } elseif ($type === 'boxes') {
                $query->where('tipo', 'LIKE', '%CAJA%');
            } elseif ($type === 'cameras') {
                $query->where('tipo', 'LIKE', '%CÁMARA%');
            } elseif ($type === 'autopartes') {
                $query->where('tipo', 'AUTOPARTE');
            }
        }

        // Filtrado por Marca
        if ($request->filled('brand') && $request->brand !== 'all') {
            $query->where('marca', $request->brand);
        }

        $items = $query->orderBy('id', 'asc')->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No se encontraron repuestos disponibles en este contenedor con los filtros seleccionados.');
        }

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(100),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $labels = $items->map(function($item) use ($writer, $barcodeGenerator) {
            $containerCode = $item->container ? substr($item->container->cod, 0, 4) : 'MK';
            $barcodeData = strtoupper($containerCode . '-' . $item->codInv);
            
            return [
                'inventario' => $item,
                'barcodeData' => $barcodeData,
                'qrCode' => base64_encode($writer->writeString($barcodeData)),
                'barcode' => base64_encode($barcodeGenerator->getBarcode($barcodeData, $barcodeGenerator::TYPE_CODE_128, 2, 40)),
            ];
        })->toArray();

        return view('labels.container-sheet', [
            'labels' => $labels,
        ]);
    }

    /**
     * Genera un pliego masivo de etiquetas en formato Carta, para los números de ítem especificados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function printLabelsByItems(Request $request)
    {
        $this->checkNotReadOnly();
        $request->validate([
            'items' => 'required|string',
        ]);

        $input = $request->input('items', '');

        // Remove the word "desde" (case-insensitive)
        $input = preg_replace('/\bdesde\b/i', '', $input);

        // Normalize alternative range words like "al", "hasta", "a", or slashes "/" to a simple hyphen "-"
        $input = preg_replace('/\s*(?:\/|al|hasta|\ba\b)\s*/i', '-', $input);

        // Collapse whitespace around hyphens to simplify parsing (e.g. "10 - 15" -> "10-15")
        $input = preg_replace('/\s*-\s*/', '-', $input);

        // Convert semicolons to commas
        $input = str_replace(';', ',', $input);

        // Split by commas or whitespace (which now represent list separators)
        $tokens = preg_split('/[,\s]+/', $input);
        $tokens = array_filter(array_map('trim', $tokens));

        $itemNumbers = [];
        foreach ($tokens as $token) {
            if (str_contains($token, '-')) {
                $parts = explode('-', $token);
                if (count($parts) === 2) {
                    $startStr = trim($parts[0]);
                    $endStr = trim($parts[1]);

                    // Pattern to match optional letters prefix and digits suffix
                    if (preg_match('/^([A-Za-z]*)([0-9]+)$/', $startStr, $startMatches) &&
                        preg_match('/^([A-Za-z]*)([0-9]+)$/', $endStr, $endMatches)) {

                        $startPrefix = $startMatches[1];
                        $startNum = (int)$startMatches[2];
                        $endPrefix = $endMatches[1];
                        $endNum = (int)$endMatches[2];

                        // If the second prefix is empty, assume it inherits from the first prefix (e.g. D0120-125)
                        if (empty($endPrefix) && !empty($startPrefix)) {
                            $endPrefix = $startPrefix;
                        }

                        if ($startPrefix === $endPrefix) {
                            $padLength = strlen($startMatches[2]);
                            $step = ($startNum <= $endNum) ? 1 : -1;

                            for ($i = $startNum; ; $i += $step) {
                                $numStr = str_pad($i, $padLength, '0', STR_PAD_LEFT);
                                $itemNumbers[] = $startPrefix . $numStr;
                                if ($i == $endNum) {
                                    break;
                                }
                            }
                        } else {
                            $itemNumbers[] = $startStr;
                            $itemNumbers[] = $endStr;
                        }
                    } else {
                        $itemNumbers[] = $startStr;
                        $itemNumbers[] = $endStr;
                    }
                } else {
                    foreach ($parts as $part) {
                        $itemNumbers[] = trim($part);
                    }
                }
            } else {
                $itemNumbers[] = $token;
            }
        }

        $itemNumbers = array_filter(array_unique($itemNumbers));


        if (empty($itemNumbers)) {
            return back()->with('error', 'Por favor ingresa al menos un número de ítem válido.');
        }

        $items = Inventario::with('container')
            ->whereIn('codInv', $itemNumbers)
            ->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'No se encontraron repuestos con los números de ítem proporcionados.');
        }

        // Ordenar los ítems en el mismo orden especificado por el usuario
        $items = $items->sortBy(function ($item) use ($itemNumbers) {
            return array_search($item->codInv, $itemNumbers);
        })->values();

        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(100),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $barcodeGenerator = new \Picqer\Barcode\BarcodeGeneratorPNG();

        $labels = $items->map(function($item) use ($writer, $barcodeGenerator) {
            $containerCode = $item->container ? substr($item->container->cod, 0, 4) : 'MK';
            $barcodeData = strtoupper($containerCode . '-' . $item->codInv);
            
            return [
                'inventario' => $item,
                'barcodeData' => $barcodeData,
                'qrCode' => base64_encode($writer->writeString($barcodeData)),
                'barcode' => base64_encode($barcodeGenerator->getBarcode($barcodeData, $barcodeGenerator::TYPE_CODE_128, 2, 40)),
            ];
        })->toArray();

        return view('labels.container-sheet', [
            'labels' => $labels,
        ]);
    }

    /**
     * Print Label 1: Maikel Cars Logo and Contact Info.
     */
    public function printLogoInfoLabel()
    {
        $this->checkNotReadOnly();
        $logoPath = public_path('logo-mk-transparent.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-mk.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-mk.jpg');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('storage/images/logo.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = storage_path('app/public/images/logo.png');
        }
        
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        return view('labels.logo-info', [
            'logoBase64' => $logoBase64,
        ]);
    }

    /**
     * Print Label 2: Unified Contact QR Code.
     */
    public function printQrCodeLabel()
    {
        $this->checkNotReadOnly();
        $qrData = "MAIKEL CARS\n" .
                  "Web: https://maikelcars.com/\n" .
                  "Instagram: @maikelcars51\n" .
                  "Telfs: 0424-5213994 / 0424-5665298";

        // Generate QR code SVG
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = base64_encode($writer->writeString($qrData));

        // Get logo Base64
        $logoPath = public_path('logo-mk-transparent.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-mk.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-mk.jpg');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('storage/images/logo.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = storage_path('app/public/images/logo.png');
        }
        
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        return view('labels.qr-code', [
            'qrCode' => $qrCode,
            'logoBase64' => $logoBase64,
            'qrData' => $qrData,
        ]);
    }

    /**
     * Print Label 3: Grid of Alternating Labels for A4/Letter Sheets.
     */
    public function printFullPageGrid()
    {
        $this->checkNotReadOnly();
        $qrData = "MAIKEL CARS\n" .
                  "Web: https://maikelcars.com/\n" .
                  "Instagram: @maikelcars51\n" .
                  "Telfs: 0424-5213994 / 0424-5665298";

        // Generate QR code SVG
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(150),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCode = base64_encode($writer->writeString($qrData));

        // Get logo Base64
        $logoPath = public_path('logo-mk-transparent.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-mk.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('logo-mk.jpg');
        }
        if (!file_exists($logoPath)) {
            $logoPath = public_path('storage/images/logo.png');
        }
        if (!file_exists($logoPath)) {
            $logoPath = storage_path('app/public/images/logo.png');
        }
        
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        return view('labels.full-page', [
            'qrCode' => $qrCode,
            'logoBase64' => $logoBase64,
            'qrData' => $qrData,
        ]);
    }

    /**
     * Muestra la lista de motores/ítems en estatus PRECIO PENDIENTE.
     */
    public function precioPendienteIndex(Request $request)
    {
        $user = auth()->user();
        $isBillingOrAdmin = $user->hasPermissionTo('manage billing') 
            || $user->hasAnyRole(['Facturacion', 'Facturación', 'Administrador', 'Superusuario', 'SUPERUSUARIO', 'ADMINISTRADOR'])
            || (stripos($user->rol, 'super') !== false || (stripos($user->rol, 'admin') !== false && stripos($user->rol, 'consulta') === false) || stripos($user->rol, 'fact') !== false);

        if (!$isBillingOrAdmin) {
            abort(403, 'No autorizado.');
        }

        $search = $request->input('search');

        // Base query for counting overall totals (without search filter)
        $baseQuery = Inventario::where(function ($query) {
            $query->whereNull('costo_importacion_unitario')
                  ->orWhere('costo_importacion_unitario', 0);
        })->where(function ($query) {
            $query->where(function ($q) {
                $q->where('tipo', 'NOT LIKE', '%AUTOPARTE%')
                  ->where('tipo', 'NOT LIKE', '%autoparte%');
            })->orWhere('status', 'PRECIO PENDIENTE');
        });

        $totals = [
            'motores' => (clone $baseQuery)->where('tipo', 'LIKE', '%MOTOR%')->count(),
            'cajas' => (clone $baseQuery)->where('tipo', 'LIKE', '%CAJA%')->count(),
            'camaras' => (clone $baseQuery)->where(function($q) {
                $q->where('tipo', 'LIKE', '%CÁMARA%')
                  ->orWhere('tipo', 'LIKE', '%CAMARA%');
            })->count(),
            'autopartes' => (clone $baseQuery)->where('tipo', 'LIKE', '%AUTOPARTE%')->count(),
            'otros' => (clone $baseQuery)->where('tipo', 'NOT LIKE', '%MOTOR%')
                                         ->where('tipo', 'NOT LIKE', '%CAJA%')
                                         ->where('tipo', 'NOT LIKE', '%CÁMARA%')
                                         ->where('tipo', 'NOT LIKE', '%CAMARA%')
                                         ->where('tipo', 'NOT LIKE', '%AUTOPARTE%')
                                         ->count(),
        ];

        // Search query
        $query = Inventario::query()
            ->select('inventarios.*')
            ->leftJoin('containers', 'inventarios.container_id', '=', 'containers.id')
            ->where(function ($query) {
                $query->whereNull('inventarios.costo_importacion_unitario')
                      ->orWhere('inventarios.costo_importacion_unitario', 0);
            })->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('inventarios.tipo', 'NOT LIKE', '%AUTOPARTE%')
                      ->where('inventarios.tipo', 'NOT LIKE', '%autoparte%');
                })->orWhere('inventarios.status', 'PRECIO PENDIENTE');
            });

        if ($search) {
            $query->where(function ($inner) use ($search) {
                $inner->where('inventarios.expediente', 'like', "%{$search}%")
                      ->orWhere('containers.cod', 'like', "%{$search}%")
                      ->orWhere('inventarios.item', 'like', "%{$search}%")
                      ->orWhere('inventarios.modelo', 'like', "%{$search}%")
                      ->orWhere('inventarios.marca', 'like', "%{$search}%")
                      ->orWhere('inventarios.tipo', 'like', "%{$search}%")
                      ->orWhere('inventarios.serial', 'like', "%{$search}%")
                      ->orWhere('inventarios.codInv', 'like', "%{$search}%");
            });
        }

        $items = $query->with('container')
            ->orderBy('inventarios.id', 'desc')
            ->paginate(15)
            ->withQueryString();


        $latestRate = \App\Models\ExchangeRate::where('source', 'BCV')->latest()->first();
        $tasaBCV = $latestRate ? (float) $latestRate->rate : 0;

        return Inertia::render('Inventario/PrecioPendiente', [
            'items' => $items,
            'filters' => $request->only(['search']),
            'totals' => $totals,
            'tasa_bcv' => $tasaBCV,
        ]);
    }

    /**
     * Actualiza el Costo de Importación de un motor y lo pone en DISPONIBLE.
     */
    public function updatePrecioPendiente(Request $request, $id)
    {
        $user = auth()->user();
        $isBillingOrAdmin = $user->hasPermissionTo('manage billing') 
            || $user->hasAnyRole(['Facturacion', 'Facturación', 'Administrador', 'Superusuario', 'SUPERUSUARIO', 'ADMINISTRADOR'])
            || (stripos($user->rol, 'super') !== false || (stripos($user->rol, 'admin') !== false && stripos($user->rol, 'consulta') === false) || stripos($user->rol, 'fact') !== false);

        if (!$isBillingOrAdmin) {
            abort(403, 'No autorizado.');
        }

        $request->validate([
            'costo_importacion_unitario' => 'required',
        ]);

        $item = Inventario::findOrFail($id);
        
        $cleanCost = $request->costo_importacion_unitario;
        if (is_string($cleanCost)) {
            $cleanCost = str_ireplace(['$', 'bs.', 'bs', ' '], '', $cleanCost);
            if (strpos($cleanCost, '.') !== false && strpos($cleanCost, ',') !== false) {
                $lastDot = strrpos($cleanCost, '.');
                $lastComma = strrpos($cleanCost, ',');
                if ($lastDot > $lastComma) {
                    $cleanCost = str_replace(',', '', $cleanCost);
                } else {
                    $cleanCost = str_replace('.', '', $cleanCost);
                    $cleanCost = str_replace(',', '.', $cleanCost);
                }
            } else {
                if (strpos($cleanCost, ',') !== false) {
                    if (preg_match('/,\d{2}$/', $cleanCost)) {
                        $cleanCost = str_replace(',', '.', $cleanCost);
                    } else {
                        $cleanCost = str_replace(',', '', $cleanCost);
                    }
                }
                if (strpos($cleanCost, '.') !== false) {
                    if (substr_count($cleanCost, '.') > 1) {
                        $cleanCost = str_replace('.', '', $cleanCost);
                    } else {
                        if (preg_match('/\.\d{2}$/', $cleanCost)) {
                            // Keep single dot as decimal separator
                        } else {
                            $cleanCost = str_replace('.', '', $cleanCost);
                        }
                    }
                }
            }
        }

        $item->costo_importacion_unitario = (float) $cleanCost;
        
        if ($item->status === 'PRECIO PENDIENTE') {
            $item->status = 'DISPONIBLE';
        }
        $item->save();

        return redirect()->back()->with('success', 'Costo de importación guardado con éxito.');
    }

    /**
     * Devuelve el tipo de vehículo y un ejemplo estimado en base a la marca, modelo y año (con caché y consulta API).
     */
    public function getVehicleType(Request $request)
    {
        $marca = $request->input('marca', '');
        $modelo = $request->input('modelo', '');
        $anoStr = $request->input('ano', '');

        $result = $this->resolveVehicleTypeInfo($marca, $modelo, $anoStr);

        return response()->json($result);
    }

    /**
     * Resuelve el tipo de vehículo y ejemplo usando caché y la API de la NHTSA.
     */
    protected function resolveVehicleTypeInfo($marca, $modelo, $anoStr)
    {
        $marca = strtoupper(trim($marca ?? ''));
        $modelo = strtoupper(trim($modelo ?? ''));
        $anoStr = trim($anoStr ?? '');

        // Generar clave única para guardar en memoria temporal (Caché)
        $cacheKey = 'vehicle_type_' . md5($marca . '_' . $modelo . '_' . $anoStr);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600 * 24, function () use ($marca, $modelo, $anoStr) {
            $tipo = 'Otro';
            $ejemplo = '';

            // 1. Intentar parsear el año de inicio
            $year = null;
            if (preg_match('/(\d{4})/', $anoStr, $matches)) {
                $year = intval($matches[1]);
            } else {
                $year = date('Y');
            }

            // 2. Diccionario local inteligente para motores e ítems comunes de Maikel Cars
            if (str_contains($marca, 'TOYOTA')) {
                if (str_contains($modelo, 'TUNDRA')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Tundra';
                } elseif (str_contains($modelo, 'TACOMA')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Tacoma';
                } elseif (str_contains($modelo, '1ZZ') || str_contains($modelo, '3ZZ') || str_contains($modelo, '2ZR') || str_contains($modelo, '3ZR') || str_contains($modelo, '1ZR')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Corolla';
                } elseif (str_contains($modelo, '2TR') || str_contains($modelo, '1KD') || str_contains($modelo, '2KD') || str_contains($modelo, '3L') || str_contains($modelo, '5L')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Hilux-Fortuner';
                } elseif (str_contains($modelo, '1GR') || str_contains($modelo, '5VZ')) {
                    $tipo = 'Camioneta / SUV';
                    $ejemplo = 'Fortuner-Merú-4Runner';
                } elseif (str_contains($modelo, '2AZ')) {
                    $tipo = 'SUV / Automóvil';
                    $ejemplo = 'RAV4-Camry';
                } elseif (str_contains($modelo, '1NZ') || str_contains($modelo, '1ND')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Yaris';
                } else {
                    // Si se pasa un modelo directo de carrocería
                    if (str_contains($modelo, 'HILUX') || str_contains($modelo, 'FORTUNER') || str_contains($modelo, 'PRADO') || str_contains($modelo, 'MERU') || str_contains($modelo, '4RUNNER') || str_contains($modelo, 'LAND CRUISER')) {
                        $tipo = 'Camioneta';
                        $ejemplo = 'Hilux-Fortuner';
                    } elseif (str_contains($modelo, 'COROLLA') || str_contains($modelo, 'YARIS') || str_contains($modelo, 'CAMRY')) {
                        $tipo = 'Automóvil';
                        $ejemplo = 'Corolla';
                    }
                }
            } elseif (str_contains($marca, 'JEEP')) {
                $tipo = 'SUV';
                if (str_contains($modelo, '3.7L') || str_contains($modelo, 'KJ') || str_contains($modelo, 'KK')) {
                    $ejemplo = 'Cherokee KJ/KK';
                } elseif (str_contains($modelo, '4.7L') || str_contains($modelo, '5.7L') || str_contains($modelo, 'COMANDER') || str_contains($modelo, 'COMMANDER')) {
                    $ejemplo = 'Grand Cherokee-Commander';
                } else {
                    $ejemplo = 'Grand Cherokee';
                }
            } elseif (str_contains($marca, 'FORD')) {
                if (str_contains($modelo, 'RANGER') || str_contains($modelo, '4.2L') || str_contains($modelo, 'COYOTE') || str_contains($modelo, '5.0L') || str_contains($modelo, '5.4L')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Ranger-F150';
                } elseif (str_contains($modelo, 'EXPLORER') || str_contains($modelo, '3.5L') || str_contains($modelo, '4.6L') || str_contains($modelo, 'ESCAPE')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Explorer-Escape';
                } elseif (str_contains($modelo, 'FUSION')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Fusion';
                } else {
                    $tipo = 'Camioneta / SUV';
                    $ejemplo = 'Ranger-F150';
                }
            } elseif (str_contains($marca, 'DODGE') || str_contains($marca, 'RAM')) {
                $tipo = 'Camioneta';
                $ejemplo = 'Dodge Ram';
            } elseif (str_contains($marca, 'CHEVROLET')) {
                if (str_contains($modelo, 'SILVERADO') || str_contains($modelo, 'AVALANCHE')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Silverado-Avalanche';
                } elseif (str_contains($modelo, 'TRAILBLAZER') || str_contains($modelo, '4.2L') || str_contains($modelo, 'TAHOE') || str_contains($modelo, 'SUBURBAN') || str_contains($modelo, '5.3L')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Trailblazer-Tahoe';
                } else {
                    $tipo = 'Automóvil / SUV';
                    $ejemplo = 'Tahoe';
                }
            } elseif (str_contains($marca, 'HYUNDAI')) {
                if (str_contains($modelo, 'SANTA FE') || str_contains($modelo, 'TUCSON') || str_contains($modelo, 'VERACRUZ')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Santa Fe-Tucson';
                } elseif (str_contains($modelo, 'GETZ') || str_contains($modelo, 'ACCENT') || str_contains($modelo, 'ELANTRA') || str_contains($modelo, 'I10') || str_contains($modelo, 'I30')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Getz-Accent-Elantra';
                } elseif (str_contains($modelo, 'H1') || str_contains($modelo, 'H-1')) {
                    $tipo = 'Van / Minivan';
                    $ejemplo = 'H1';
                } else {
                    $tipo = 'Automóvil / SUV';
                    $ejemplo = 'Accent-Tucson';
                }
            } elseif (str_contains($marca, 'HONDA')) {
                if (str_contains($modelo, 'CRV') || str_contains($modelo, 'CR-V') || str_contains($modelo, 'PILOT')) {
                    $tipo = 'SUV';
                    $ejemplo = 'CR-V';
                } elseif (str_contains($modelo, 'CIVIC') || str_contains($modelo, 'ACCORD') || str_contains($modelo, 'FIT')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Civic-Fit';
                } else {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Civic';
                }
            } elseif (str_contains($marca, 'MITSUBISHI')) {
                if (str_contains($modelo, 'MONTERO') || str_contains($modelo, 'OUTLANDER') || str_contains($modelo, 'NATIVA')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Montero-Outlander';
                } elseif (str_contains($modelo, 'L200')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'L200';
                } elseif (str_contains($modelo, 'LANCER') || str_contains($modelo, 'MIVEC') || str_contains($modelo, 'CK') || str_contains($modelo, 'SIGNUM')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Lancer';
                } else {
                    $tipo = 'Automóvil / SUV';
                    $ejemplo = 'Lancer-Montero';
                }
            } elseif (str_contains($marca, 'NISSAN')) {
                if (str_contains($modelo, 'PATHFINDER') || str_contains($modelo, 'XTRAIL') || str_contains($modelo, 'X-TRAIL') || str_contains($modelo, 'PATROL') || str_contains($modelo, 'MURANO')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Patrol-Xtrail';
                } elseif (str_contains($modelo, 'FRONTIER') || str_contains($modelo, 'NAVARA')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Frontier';
                } elseif (str_contains($modelo, 'SENTRA') || str_contains($modelo, 'TIIDA') || str_contains($modelo, 'ALMERA') || str_contains($modelo, 'VERSA') || str_contains($modelo, 'MARCH')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Sentra-Tiida';
                } else {
                    $tipo = 'Automóvil / SUV';
                    $ejemplo = 'Sentra-Patrol';
                }
            } elseif (str_contains($marca, 'KIA')) {
                if (str_contains($modelo, 'SPORTAGE') || str_contains($modelo, 'SORENTO')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Sportage';
                } elseif (str_contains($modelo, 'RIO') || str_contains($modelo, 'PICANTO') || str_contains($modelo, 'CERATO')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Rio-Picanto';
                } elseif (str_contains($modelo, 'PREGIO') || str_contains($modelo, 'CARNIVAL')) {
                    $tipo = 'Minivan';
                    $ejemplo = 'Pregio';
                } else {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Rio';
                }
            } elseif (str_contains($marca, 'MAZDA')) {
                if (str_contains($modelo, 'MAZDA 3') || str_contains($modelo, 'MAZDA 6') || str_contains($modelo, 'MAZDA 2') || str_contains($modelo, 'ALLEGRO')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Mazda 3';
                } elseif (str_contains($modelo, 'BT-50') || str_contains($modelo, 'BT50')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'BT-50';
                } elseif (str_contains($modelo, 'CX')) {
                    $tipo = 'SUV';
                    $ejemplo = 'CX-5';
                } else {
                    $tipo = 'Automóvil / SUV';
                    $ejemplo = 'Mazda 3';
                }
            } elseif (str_contains($marca, 'CHERY')) {
                if (str_contains($modelo, 'TIGGO')) {
                    $tipo = 'SUV';
                    $ejemplo = 'Tiggo';
                } elseif (str_contains($modelo, 'QQ') || str_contains($modelo, 'ARAUCANA') || str_contains($modelo, 'ORINOCO')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'QQ-Orinoco';
                } else {
                    $tipo = 'Automóvil';
                    $ejemplo = 'QQ';
                }
            }

            // 3. Consultar la API pública de la NHTSA para obtener/validar modelos oficiales de la marca y año
            if ($marca && $year) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(5)
                        ->get("https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMakeYear/make/" . urlencode(strtolower($marca)) . "/modelyear/" . urlencode($year) . "?format=json");
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        $models = $data['Results'] ?? [];
                        
                        foreach ($models as $m) {
                            $name = strtoupper($m['Model_Name'] ?? '');
                            if (str_contains($name, 'COROLLA') || str_contains($name, 'YARIS') || str_contains($name, 'CAMRY')) {
                                if (str_contains($modelo, 'COROLLA') || str_contains($modelo, 'YARIS') || str_contains($modelo, 'CAMRY')) {
                                    $tipo = 'Automóvil';
                                }
                            } elseif (str_contains($name, 'HILUX') || str_contains($name, 'TUNDRA') || str_contains($name, 'TACOMA') || str_contains($name, 'F-150') || str_contains($name, 'RAM') || str_contains($name, 'SILVERADO')) {
                                if (str_contains($modelo, 'HILUX') || str_contains($modelo, 'RAM') || str_contains($modelo, 'SILVERADO') || str_contains($modelo, '2TR')) {
                                    $tipo = 'Camioneta';
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning("NHTSA API call failed: " . $e->getMessage());
                }
            }

            if ($tipo === 'Otro') {
                $modeloUpper = strtoupper($modelo);
                if (preg_match('/(SANTA\s?FE|TUCSON|SPORTAGE|SORENTO|EXPLORER|CHEROKEE|VITARA|MONTERO|OUTLANDER|NATIVA|PATHFINDER|X-?TRAIL|MURANO|TAHOE|BLAZER|RUNNER|FORTUNER|PRADO|MERU|ESCAPE|DUSTER|TIGGO)/', $modeloUpper)) {
                    $tipo = 'SUV';
                    $ejemplo = 'Modelo Detectado';
                } elseif (preg_match('/(HILUX|TUNDRA|TACOMA|D-?MAX|RANGER|SILVERADO|RAM|L200|FRONTIER|NAVARA|BT-?50|TIGER|F-?150|CAMIONETA|PICKUP)/', $modeloUpper)) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'Modelo Detectado';
                } elseif (preg_match('/(COROLLA|YARIS|CIVIC|AVEO|OPTRA|SPARK|FIT|LANCER|GETZ|RIO|ACCENT|ELANTRA|PICANTO|CERATO|MAZDA\s?[236]|ALLEGRO|CLIO|LOGAN|SYMBOL|MEGANE|UNO|PALIO|SIENA|QQ|ORINOCO|MIVEC)/', $modeloUpper)) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'Modelo Detectado';
                } elseif (str_contains($modeloUpper, 'TRUCK') || str_contains($modeloUpper, 'PICKUP') || str_contains($modeloUpper, 'CABIN')) {
                    $tipo = 'Camioneta';
                    $ejemplo = 'General';
                } elseif (str_contains($modeloUpper, 'SEDAN') || str_contains($modeloUpper, 'HATCHBACK')) {
                    $tipo = 'Automóvil';
                    $ejemplo = 'General';
                } else {
                    $tipo = 'Otro / No Clasificado';
                    $ejemplo = 'N/A';
                }
            }

            return [
                'tipo_vehiculo' => $tipo,
                'ejemplo' => $tipo . ' (' . $ejemplo . ')',
                'cached_at' => now()->toIso8601String(),
            ];
        });
    }

    private function checkNotReadOnly()
    {
        $user = auth()->user();
        if ($user) {
            $hasPermission = $user->hasAnyPermission(['manage partida', 'manage billing']) 
                || $user->hasAnyRole(['Superusuario', 'Administrador', 'Inventario'])
                || (stripos($user->rol, 'super') !== false || stripos($user->rol, 'admin') !== false && stripos($user->rol, 'consulta') === false || stripos($user->rol, 'inventario') !== false);
            if (!$hasPermission) {
                abort(403, 'No autorizado.');
            }
        }
    }
}

