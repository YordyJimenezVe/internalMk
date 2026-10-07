<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Inventario;
use App\Models\Bitacora;
use App\Models\ReverseBill;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\ExchangeRate;
use App\Models\BillingRequest;
use App\Models\Maintenance;
use Carbon\Carbon;

/**
 * Controlador para la facturación, registro de ventas y devoluciones.
 * 
 * Este controlador administra el ciclo comercial: creación y edición de facturas,
 * obtención automática de la tasa oficial de cambio del Banco Central de Venezuela (BCV),
 * procesamiento de devoluciones (totales, por garantía o desincorporación),
 * registro de auditoría en Bitácora y generación de facturas en PDF.
 */
class BillingsController extends Controller
{
    /**
     * Crea un registro de auditoría en la Bitácora de cambios para facturación.
     *
     * @param  string  $action  Acción realizada (UPDATE, DELETE, REVERSE, etc.).
     * @param  string|int  $billingId  Identificador o número de la factura.
     * @param  string  $field  Campo modificado en caso de actualización.
     * @param  string  $oldValue  Valor anterior.
     * @param  string  $newValue  Valor nuevo asignado.
     * @return void
     */
    private function createBitacoraEntry($action, $billingId, $field = '', $oldValue = '', $newValue = '')
    {
        if ($action == 'UPDATE') {
            $action = 'UPDATE';
            $descrip = "Factura: $billingId, $field: $oldValue, $newValue";
        } else if ($action == 'DELETE') {
            $action = 'DELETE';
            $descrip = "Factura: $billingId";
        } else if ($action == 'REVERSE') {
            $action = 'REVERSE';
            $descrip = "Factura: $billingId";
        }
        Bitacora::create([
            'users_id' => Auth::user()->id,
            'action' => $action,
            'description' => $descrip,
        ]);
    }
    /**
     * Muestra el listado completo de facturas registradas en orden descendente.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $billings = Billing::with(['partida', 'inventario', 'partidas', 'inventarios', 'billingRequests'])
            ->orderBy('id', 'desc')
            ->get();
        return inertia('Bill/Index', [
            'Facturas' => $billings
        ]);
    }

    /**
     * Muestra el formulario de creación de factura para un artículo específico del inventario.
     * 
     * Resuelve los datos de cotización (tasa de cambio oficial del BCV) consultándolos
     * en tiempo real mediante Guzzle e integrando datos previos si provienen de una solicitud de facturación.
     *
     * @param  \Illuminate\Http\Request  $request  Petición con identificadores opcionales de solicitud.
     * @param  string|int  $id  Identificador único del artículo a facturar.
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
     */
    public function create(Request $request, $id)
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO']) && !$user->hasAnyRole(['Superusuario', 'Administrador', 'SUPERUSUARIO', 'ADMINISTRADOR'])) {
            return app(\App\Http\Controllers\ScanController::class)->directToMaintenance($id);
        }

        $requestId = $request->input('request_id');
        $billing = Inventario::with('container')->findOrFail($id);

        $requestTasa = null;
        $requestedPriceUsd = null;
        if ($requestId) {
            $billingRequest = BillingRequest::find($requestId);
            if ($billingRequest) {
                $requestedPriceUsd = (float) $billingRequest->price;
                $billing->price_sale = (string) $billingRequest->price;
                $billing->client_name = $billingRequest->client_name;
                $billing->client_cedula = $billingRequest->client_cedula;
                $billing->client_phone = $billingRequest->client_phone;
                $billing->client_address = $billingRequest->client_address;
                if (!empty($billingRequest->observation)) {
                    if (!empty($billing->observation) && strpos($billing->observation, $billingRequest->observation) === false) {
                        $billing->observation = $billing->observation . ' | ' . $billingRequest->observation;
                    } else {
                        $billing->observation = $billingRequest->observation;
                    }
                }
                $billing->billing_request_id = $requestId;
                $billing->client_cedula_url = $billingRequest->client_cedula_file ? asset('storage/' . $billingRequest->client_cedula_file) : null;
                if ($billingRequest->tasa_bcv && (float) $billingRequest->tasa_bcv > 0) {
                    $requestTasa = (float) $billingRequest->tasa_bcv;
                }
            }

            // Remove the notification for the current user who is taking the request
            $user->notifications()
                ->where('data->billing_request_id', $requestId)
                ->delete();
        }

        $billing->serial_image_url = $billing->serial_image_path ? asset('storage/' . $billing->serial_image_path) : null;

        $now = Carbon::now();
        $today = Carbon::today();
        $nineAm = $today->copy()->setHour(9)->setMinute(0);
        $twoPm = $today->copy()->setHour(14)->setMinute(0);

        $latestRate = ExchangeRate::where('source', 'BCV')->latest()->first();
        $tasa = $latestRate ? (float) $latestRate->rate : 0;
        $shouldFetch = false;

        if (!$latestRate) {
            $shouldFetch = true;
        } else {
            $lastUpdate = $latestRate->created_at;
            if ($now->greaterThanOrEqualTo($twoPm)) {
                if ($lastUpdate->lessThan($twoPm))
                    $shouldFetch = true;
            } elseif ($now->greaterThanOrEqualTo($nineAm)) {
                if ($lastUpdate->lessThan($nineAm))
                    $shouldFetch = true;
            }
            // Antes de las 9 AM usamos la última que tengamos (usualmente la de ayer tarde)
        }

        if ($shouldFetch) {
            try {
                $client = new Client([
                    'verify' => false,
                    'timeout' => 5,
                    'connect_timeout' => 5,
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml,xml;q=0.9,image/webp,*/*;q=0.8',
                    ]
                ]);

                $response = $client->request('GET', 'https://www.bcv.org.ve/');
                $html = $response->getBody()->getContents();
                $crawler = new Crawler($html);
                $tasaRaw = $crawler->filter('#dolar strong')->text();
                $newTasa = (float) str_replace(',', '.', trim($tasaRaw));

                if ($newTasa > 0) {
                    ExchangeRate::create(['rate' => $newTasa, 'source' => 'BCV']);
                    $tasa = $newTasa;
                }
            } catch (\Exception $e) {
                // Si falla, mantenemos la $tasa previa (la última en DB) o 0 si no había nada
            }
        }

        $savedRate = (float) ($billing->tasa_bcv ?? ($billing->container->tasa_bcv ?? 0));
        if ($savedRate <= 0 && (float) ($billing->costo ?? 0) > 0 && (float) ($billing->costo_importacion_unitario ?? 0) > 0) {
            $savedRate = (float) $billing->costo_importacion_unitario / (float) $billing->costo;
        }

        // 1. Obtener Costo Base en USD
        $costoUsd = (float) ($billing->costo ?? 0);
        if ($costoUsd <= 0 && (float) ($billing->costo_importacion_unitario ?? 0) > 0) {
            if ($savedRate > 0) {
                $costoUsd = (float) $billing->costo_importacion_unitario / $savedRate;
            } elseif ($tasa > 0) {
                $costoUsd = (float) $billing->costo_importacion_unitario / $tasa;
            }
        }

        // 2. Sumar la base imponible de repuestos/servicios de taller conciliados con FACTURA (en Bs.)
        $mantenimientosFacturablesBs = 0;
        foreach ($billing->maintenances as $maint) {
            $mantenimientosFacturablesBs += (float) $maint->items()
                ->where('document_type', 'FACTURA')
                ->where('status', 'CONCILIADO')
                ->sum('base_imponible');
        }
        $mantenimientosUsd = $tasa > 0 ? ($mantenimientosFacturablesBs / $tasa) : 0;

        // 3. Evaluar Prorrateo de Gastos y Porcentaje de Utilidad
        if ($billing->price && (float) $billing->price > 0 && $savedRate > 0) {
            $costoDeclarado = ((float) $billing->price / $savedRate) + $mantenimientosUsd;
        } else {
            $prorrateoBs = (float) ($billing->prorrateo_gastos ?? 0);
            $hasProrrateo = $prorrateoBs > 0;
            $rateForProrrateo = $savedRate > 0 ? $savedRate : $tasa;

            if ($hasProrrateo) {
                $prorrateoUsd = $rateForProrrateo > 0 ? ($prorrateoBs / $rateForProrrateo) : 0;
                $costoLandedUsd = $costoUsd + $prorrateoUsd + $mantenimientosUsd;
                $utilityPercentage = $billing->porcentaje_utilidad !== null 
                    ? (float) $billing->porcentaje_utilidad 
                    : (float) \App\Models\Setting::get('utility_percentage', 30);

                $costoDeclarado = $costoLandedUsd * (1 + ($utilityPercentage / 100));
            } else {
                // Si NO tiene prorrateo asignado: solo se aplica el costo base USD + taller facturable en USD
                $costoDeclarado = $costoUsd + $mantenimientosUsd;
            }
        }

        return inertia('Bill/Create', [
            'data' => $billing,
            'tasa_bcv' => $tasa,
            'costo_declarado' => $costoDeclarado,
        ]);
    }

    /**
     * Almacena una nueva factura recién creada en la base de datos.
     * 
     * Limpia formatos numéricos locales, asocia el usuario que registra la venta,
     * actualiza el estado del artículo en inventario a 'VENDIDO', registra el precio real de venta
     * y marca como procesada la solicitud de facturación asociada si existiera.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos de facturación.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $valorD = $request->input('divisa');

        // Helper function to clean numeric input
        $cleanNumeric = function ($value) {
            if (empty($value))
                return 0;
            // If it has both , and . the last one is the decimal
            if (strpos($value, '.') !== false && strpos($value, ',') !== false) {
                if (strrpos($value, '.') > strrpos($value, ',')) {
                    return (float) str_replace(',', '', $value);
                } else {
                    return (float) str_replace(['.', ','], ['', '.'], $value);
                }
            }
            // If it only has one separator
            if (strpos($value, ',') !== false) {
                // If there are exactly 2 digits after comma, it's a decimal
                if (preg_match('/,\d{2}$/', $value)) {
                    return (float) str_replace(['.', ','], ['', '.'], $value);
                }
                return (float) str_replace(',', '', $value);
            }
            if (strpos($value, '.') !== false) {
                // If there are exactly 2 digits after dot, it's a decimal
                if (preg_match('/\.\d{2}$/', $value)) {
                    return (float) $value;
                }
                return (float) str_replace('.', '', $value);
            }
            return (float) $value;
        };

        $partida = new Billing();
        $partida->fill($request->all());

        $numericDivisa = $cleanNumeric($valorD);
        $partida->divisa = $numericDivisa;

        // Use the full sale price for the dashboard total
        $salePrice = $cleanNumeric($request->input('priceDivisa'));
        $partida->total = $salePrice > 0 ? $salePrice : $numericDivisa;

        $partida->user_id = Auth::id(); // Assign current user
        $partida->save();

        // 1. Update Inventario Status and Record the actual sale price
        $inventario = Inventario::findOrFail($request->partida_id);
        $inventario->update([
            'status' => 'VENDIDO',
            'price_sale' => $partida->total
        ]);

        // 2. Handle Billing Request
        $requestId = $request->input('billing_request_id');
        if ($requestId) {
            $billingRequest = \App\Models\BillingRequest::find($requestId);
            if ($billingRequest) {
                $billingRequest->update(['status' => 'processed']);
            }
            // Delete notifications for ALL users since the sale is finished!
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where(function($query) use ($requestId) {
                    $query->where('data->billing_request_id', $requestId)
                          ->orWhere('data', 'like', '%"billing_request_id":' . $requestId . '%');
                })
                ->delete();
        }

        // Also clean up any pending requests for this motor/partida
        $pendingRequests = \App\Models\BillingRequest::where('partida_id', $inventario->id)
            ->where('status', 'pending')
            ->get();
            
        foreach ($pendingRequests as $pReq) {
            $pReq->update(['status' => 'processed']);
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where(function($query) use ($pReq) {
                    $query->where('data->billing_request_id', $pReq->id)
                          ->orWhere('data', 'like', '%"billing_request_id":' . $pReq->id . '%');
                })
                ->delete();
        }

        // Run full cleanup
        \App\Models\BillingRequest::cleanupNotifications();

        // Notify via Telegram Group
        $itemName = $inventario ? "{$inventario->marca} {$inventario->modelo}" : 'Ítem';
        $telegramMessage = "✅ <b>Factura Registrada</b>\n\n"
            . "📄 <b>Factura:</b> #{$partida->numero_factura}\n"
            . "📦 <b>Artículo:</b> {$itemName}\n"
            . "👤 <b>Cliente:</b> {$partida->client_name}\n"
            . "💵 <b>Monto:</b> $" . number_format($partida->total, 2) . "\n"
            . "👤 <b>Registrada por:</b> " . Auth::user()->name;
        \App\Services\TelegramService::sendMessage($telegramMessage);

        $redirect = redirect()->route('billing')->with('success', 'Factura registrada con éxito.')->with('billing_ids', [$partida->id]);
        
        $tipo = strtoupper($partida->tipo_item ?? ($inventario->tipo ?? ''));
        if (str_contains($tipo, 'MOTOR')) {
            $redirect = $redirect->with('warranty_ids', [$partida->id]);
        }

        return $redirect;
    }

    /**
     * Muestra los detalles de una factura específica (Inactivo, redirigido a show de inventario).
     *
     * @param  string  $id  Identificador único de la factura.
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario para editar los datos de facturación de una factura existente.
     *
     * @param  \App\Models\Billing  $bill  Instancia de la factura inyectada por Implicit Model Binding.
     * @return \Inertia\Response
     */
    public function edit(Billing $bill)
    {
        $bill->load('inventario');
        return inertia('Bill/Edit', [
            'bill' => $bill,
        ]);
    }

    /**
     * Actualiza una factura específica y registra las modificaciones en la Bitácora.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos modificados.
     * @param  int  $id  Identificador único de la factura.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id)
    {
        $billing = Billing::findOrFail($id);
        $originalValues = $billing->getOriginal();
        $campos = $request->all();
        $coleccionA = collect($originalValues);
        $coleccionB = collect($campos);
        $indicesComunes = $coleccionA->intersectByKeys($coleccionB)->keys();
        foreach ($indicesComunes as $indice => $value) {
            $original = $coleccionA[$value];
            $campos = $coleccionB[$value];
            if ($original != $campos) {
                $this->createBitacoraEntry('UPDATE', $billing->numero_factura, $indicesComunes[$indice], 'Valor Original: ' . $original, 'Valor Nuevo: ' . $campos);
            }
        }
        $billing->fill($request->all());
        $billing->save();
        return redirect()->route('billing');
    }

    /**
     * Elimina una factura específica de la base de datos y audita la acción.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP.
     * @param  int  $id  Identificador único de la factura a eliminar.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, int $id)
    {
        //$billing = Billing::findOrFail($id);
        $billing = Billing::with(['partida', 'partidas'])->findOrFail($id);
        $marca = $billing->partidas->first()->marca;
        $modelo = $billing->partidas->first()->modelo;
        $billing->delete();
        $this->createBitacoraEntry('DELETE', $billing->numero_factura . " " . $marca . " " . $modelo);
        return redirect()->route('billing');
    }

    /**
     * Muestra el formulario para procesar una devolución o nota de crédito sobre una factura.
     *
     * @param  \App\Models\Billing  $partida  Instancia de la factura (herencia de ruta).
     * @param  string|int  $id  Identificador único de la factura.
     * @return \Inertia\Response
     */
    public function return(Billing $partida, $id)
    {
        $data = Billing::with(['partida.container'])->findOrFail($id);

        $availableItems = Inventario::where('status', 'DISPONIBLE')
            ->where('id', '!=', $data->partida_id)
            ->select('id', 'codInv', 'tipo', 'marca', 'modelo', 'serial', 'año', 'price')
            ->orderBy('id', 'desc')
            ->get();

        return inertia('Bill/Return', [
            'bill' => $data,
            'availableItems' => $availableItems,
        ]);
    }

    /**
     * Consulta y valida el estatus de un ítem de inventario por su ID para operaciones de cambio.
     *
     * @param  int  $id  Identificador único del ítem en inventario.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkItem(int $id)
    {
        $item = Inventario::with('container')->find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró ningún elemento con el ID especificado (#' . $id . ').',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'codInv' => $item->codInv,
                'tipo' => $item->tipo,
                'marca' => $item->marca,
                'modelo' => $item->modelo,
                'serial' => $item->serial,
                'año' => $item->año,
                'status' => $item->status,
                'price' => $item->price,
                'expediente' => $item->expediente ?? ($item->container ? $item->container->expediente : null),
                'is_available' => $item->status === 'DISPONIBLE',
            ],
        ]);
    }

    /**
     * Procesa la solicitud de devolución (Total, Temporal/Garantía, Desincorporación o Cambio de Ítem),
     * actualizando el inventario, generando la nota de crédito (ReverseBill) y auditando la bitácora.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con los datos de nota de crédito.
     * @param  int  $id  Identificador único de la factura a anular/devolver.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function returnSubmit(Request $request, int $id)
    {
        $billing = Billing::with('partida')->findOrFail($id);
        $returnType = $request->input('return_type', 'TOTAL');
        $inventario = $billing->partida;

        // Validar tipo de devolución
        if (!in_array($returnType, ['TOTAL', 'TEMPORAL', 'DESINCORPORACION', 'CAMBIO'])) {
            return back()->withErrors(['return_type' => 'Tipo de devolución no válido.']);
        }

        // Si es CAMBIO, validar el nuevo ítem entrante
        $newItem = null;
        if ($returnType === 'CAMBIO') {
            $request->validate([
                'nuevo_partida_id' => 'required|integer|exists:inventarios,id',
            ], [
                'nuevo_partida_id.required' => 'Debe ingresar o seleccionar el ID del nuevo elemento (motor, caja, cámara, etc.).',
                'nuevo_partida_id.exists' => 'El elemento indicado con ese ID no existe en el inventario.',
            ]);

            $nuevoPartidaId = (int) $request->input('nuevo_partida_id');
            if ($inventario && $nuevoPartidaId === (int) $inventario->id) {
                return back()->withErrors(['nuevo_partida_id' => 'El nuevo elemento no puede ser el mismo elemento actual de la factura.']);
            }

            $newItem = Inventario::findOrFail($nuevoPartidaId);
            if ($newItem->status !== 'DISPONIBLE') {
                return back()->withErrors(['nuevo_partida_id' => "El elemento #{$newItem->id} ({$newItem->tipo} {$newItem->marca}) no está DISPONIBLE (Estatus actual: {$newItem->status})."]);
            }
        }

        // 1. Determinar nuevo estado para el inventario saliente
        $newStatus = 'DISPONIBLE';
        $actionVerb = 'DEVOLUCIÓN TOTAL';

        if ($returnType === 'TEMPORAL') {
            $newStatus = 'GARANTIA';
            $actionVerb = 'DEVOLUCIÓN POR GARANTÍA';
        } elseif ($returnType === 'DESINCORPORACION') {
            $newStatus = 'DESINCORPORADO';
            $actionVerb = 'DESINCORPORACIÓN';
        } elseif ($returnType === 'CAMBIO') {
            $oldItemDisposition = $request->input('old_item_status', 'GARANTIA');
            $newStatus = in_array($oldItemDisposition, ['DISPONIBLE', 'DESINCORPORADO']) ? $oldItemDisposition : 'GARANTIA';
            $actionVerb = 'CAMBIO DE PRODUCTO';
        }

        // 2. Actualizar Inventario (Ítem actual/saliente)
        if ($inventario) {
            $inventario->update(['status' => $newStatus]);

            // Si es TEMPORAL o CAMBIO con destino a GARANTÍA, generar ticket de mantenimiento/revisión técnica
            if ($returnType === 'TEMPORAL' || ($returnType === 'CAMBIO' && $newStatus === 'GARANTIA')) {
                $maintDesc = $returnType === 'CAMBIO'
                    ? "CAMBIO DE PRODUCTO / GARANTÍA. FACTURA ORIGINAL: #" . ($billing->numero_factura ?? 'S/N') . ". REEMPLAZADO POR NUEVO ÍTEM #{$newItem->id} ({$newItem->tipo} {$newItem->marca} {$newItem->modelo})"
                    : 'DEVOLUCIÓN TEMPORAL POR GARANTÍA. FACTURA ORIGINAL: #' . ($billing->numero_factura ?? 'S/N');

                \App\Models\Maintenance::create([
                    'fecha' => now()->format('Y-m-d'),
                    'descripcion' => $maintDesc,
                    'tipo' => 'GARANTÍA',
                    'status' => 'EN ESPERA',
                    'partida_id' => $inventario->id,
                    'cedula_mecanico' => 0,
                    'nombre_mecanico' => 'POR',
                    'apellido_mecanico' => 'ASIGNAR',
                    'observaciones' => 'Creado automáticamente tras ' . strtolower($actionVerb) . ' de la factura #' . ($billing->numero_factura ?? 'S/N') . '.',
                ]);
            }
        }

        // 3. Si es CAMBIO, actualizar nuevo elemento entrante a VENDIDO
        if ($returnType === 'CAMBIO' && $newItem) {
            $newItem->update([
                'status' => 'VENDIDO',
                'price_sale' => $billing->total ?? $newItem->price,
            ]);
        }

        // 4. Registrar Reverse Bill (Nota de Crédito)
        ReverseBill::create([
            'users_id' => Auth::user()->id,
            'numero_factura' => $request->input('numero_factura') ?? $billing->numero_factura ?? 'S/N',
            'numero_control' => $request->input('numero_control') ?? $billing->numero_control ?? 'S/N',
            'numero_nota_credito' => $request->input('numero_nota_credito') ?? 'S/N',
            'numero_factura_afect' => $request->input('numero_factura_afect') ?? $billing->numero_factura_afect ?? $billing->numero_factura ?? 'S/N',
        ]);

        // 5. Entrada en Bitácora
        if ($returnType === 'CAMBIO') {
            $descLog = mb_strtoupper("{$actionVerb} EN FACTURA: {$billing->numero_factura}. " .
                "ÍTEM ANTERIOR (#" . ($inventario ? $inventario->id : 'N/A') . "): " . ($inventario ? "{$inventario->tipo} {$inventario->marca} {$inventario->modelo} SERIAL: {$inventario->serial}" : 'N/A') . " (PASÓ A: {$newStatus}) -> " .
                "NUEVO ÍTEM (#{$newItem->id}): {$newItem->tipo} {$newItem->marca} {$newItem->modelo} SERIAL: {$newItem->serial} (PASÓ A: VENDIDO). " .
                "NOTA CRÉDITO: " . ($request->input('numero_nota_credito') ?? 'S/N'));
        } else {
            $descLog = mb_strtoupper("{$actionVerb} DE FACTURA: {$billing->numero_factura}. " .
                "NOTA CRÉDITO: " . ($request->input('numero_nota_credito') ?? 'S/N') . ". " .
                "ESTADO DE ITEM (#" . ($inventario ? $inventario->id : 'N/A') . "): {$newStatus}");
        }

        Bitacora::create([
            'users_id' => Auth::user()->id,
            'action' => mb_strtoupper($actionVerb),
            'description' => $descLog,
        ]);

        // 6. Actualizar Factura
        if ($returnType === 'CAMBIO') {
            // En CAMBIO, la factura se mantiene activa y se actualiza el partida_id con el nuevo ítem
            $motivoCambio = $request->input('motivo_cambio') ? " | MOTIVO: " . trim($request->input('motivo_cambio')) : '';
            $obsLog = "CAMBIO DE ARTÍCULO [" . now()->format('d/m/Y H:i') . "]: ÍTEM ANTERIOR #" . ($inventario ? $inventario->id . " ({$inventario->tipo} {$inventario->marca} {$inventario->modelo} SERIAL: {$inventario->serial})" : 'N/A') .
                " REEMPLAZADO POR NUEVO ÍTEM #{$newItem->id} ({$newItem->tipo} {$newItem->marca} {$newItem->modelo} SERIAL: {$newItem->serial}). NC: " . ($request->input('numero_nota_credito') ?? 'S/N') . $motivoCambio;

            $billing->update([
                'fecha' => $request->input('fecha_cambio') ?? now()->format('Y-m-d'),
                'hora' => now()->format('H:i:s'),
                'partida_id' => $newItem->id,
                'numero_nota_credito' => $request->input('numero_nota_credito') ?? $billing->numero_nota_credito,
                'numero_factura_afect' => $request->input('numero_factura_afect') ?? $billing->numero_factura_afect ?? $billing->numero_factura,
                'observaciones' => trim(($billing->observaciones ? $billing->observaciones . " 
---
 " : "") . $obsLog),
            ]);
        } elseif ($returnType !== 'TEMPORAL') {
            // TOTAL o DESINCORPORACION anulan la factura
            $billing->update(['status' => 'ANULADA']);
        }

        // 7. Notificación vía Telegram
        if ($returnType === 'CAMBIO') {
            $oldName = $inventario ? "{$inventario->tipo} {$inventario->marca} {$inventario->modelo} (ID: #{$inventario->id}, Serial: {$inventario->serial})" : 'Ítem Anterior';
            $newName = "{$newItem->tipo} {$newItem->marca} {$newItem->modelo} (ID: #{$newItem->id}, Serial: {$newItem->serial})";
            $notaCredito = $request->input('numero_nota_credito') ?? 'S/N';
            $telegramMessage = "🔄 <b>Cambio de Ítem en Factura</b>

"
                . "📄 <b>Factura:</b> #{$billing->numero_factura}
"
                . "🧾 <b>Nota de Crédito:</b> #" . ($request->input('numero_nota_credito') ?? 'S/N') . "
"
                . "🔴 <b>Ítem Saliente:</b> {$oldName}
"
                . "🟢 <b>Nuevo Ítem Asignado:</b> {$newName}
"
                . "📦 <b>Estatus Ítem Saliente:</b> {$newStatus}
"
                . "👤 <b>Procesado por:</b> " . Auth::user()->name;
        } else {
            $itemName = $inventario ? "{$inventario->marca} {$inventario->modelo}" : 'Ítem';
            $notaCredito = $request->input('numero_nota_credito') ?? 'S/N';
            $telegramMessage = "⚠️ <b>Registro de Devolución</b>

"
                . "⚙️ <b>Tipo:</b> {$actionVerb}
"
                . "📄 <b>Factura Afectada:</b> #{$billing->numero_factura}
"
                . "🧾 <b>Nota de Crédito:</b> #{$notaCredito}
"
                . "📦 <b>Motor/Ítem:</b> {$itemName}
"
                . "👤 <b>Procesado por:</b> " . Auth::user()->name;
        }
        \App\Services\TelegramService::sendMessage($telegramMessage);

        return redirect()->route('billing')->with('success', "{$actionVerb} procesada con éxito.");
    }


    /**
     * Genera y transmite el PDF de la factura utilizando DomPDF.
     *
     * @param  string|int  $id  Identificador único de la factura.
     * @return \Illuminate\Http\Response
     */
    public function pdf($id)
    {
        $bill = Billing::with('partida')->findOrFail($id);

        $pdf = \PDF::loadView('reports.invoice', compact('bill'));

        // Define paper size and orientation (optional, already set in view or config)
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Factura-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Genera y transmite el PDF de la Póliza de Garantía de Motor utilizando DomPDF.
     *
     * @param  string|int  $id  Identificador único de la factura.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function warrantyPdf($id)
    {
        $bill = Billing::with(['partida', 'billingRequests'])->findOrFail($id);

        $tipo = strtoupper($bill->tipo_item ?? ($bill->partida->tipo ?? ''));
        if (!str_contains($tipo, 'MOTOR')) {
            return redirect()->back()->with('error', 'La póliza de garantía solo aplica para motores.');
        }

        $pdf = \PDF::loadView('reports.warranty', compact('bill'));
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Garantia-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}