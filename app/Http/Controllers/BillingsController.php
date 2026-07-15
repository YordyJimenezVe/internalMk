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
        $billings = Billing::with(['partida', 'inventario', 'partidas', 'inventarios'])
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
        $billing = Inventario::findOrFail($id);

        if ($requestId) {
            $billingRequest = BillingRequest::find($requestId);
            if ($billingRequest) {
                $billing->price = $billingRequest->price;
                $billing->client_name = $billingRequest->client_name;
                $billing->client_cedula = $billingRequest->client_cedula;
                $billing->client_phone = $billingRequest->client_phone;
                $billing->client_address = $billingRequest->client_address;
                $billing->client_email = $billingRequest->client_email;
                $billing->billing_request_id = $requestId;
                $billing->client_cedula_url = $billingRequest->client_cedula_file ? asset('storage/' . $billingRequest->client_cedula_file) : null;
            }

            // Remove the notification for the current user who is taking the request
            $user->notifications()
                ->where('data->billing_request_id', $requestId)
                ->delete();
        }

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

        $baseCosto = (float) ($billing->costo_importacion_unitario ?? $billing->costo ?? 0);
        
        // Sumar la base imponible de todos los repuestos/servicios externos conciliados con Factura
        $mantenimientosFacturables = 0;
        foreach ($billing->maintenances as $maint) {
            $mantenimientosFacturables += (float) $maint->items()
                ->where('document_type', 'FACTURA')
                ->where('status', 'CONCILIADO')
                ->sum('base_imponible');
        }

        $utilidad = (float) \App\Models\Setting::get('utility_percentage', 30);
        $costoDeclaradoBs = ($baseCosto + $mantenimientosFacturables) * (1 + $utilidad / 100);
        $costoDeclarado = $tasa > 0 ? ($costoDeclaradoBs / $tasa) : 0;

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
                ->where('data->billing_request_id', $requestId)
                ->delete();
        }

        // Notify via Telegram Group
        $itemName = $inventario ? "{$inventario->marca} {$inventario->modelo}" : 'Ítem';
        $telegramMessage = "✅ <b>Factura Registrada</b>\n\n"
            . "📄 <b>Factura:</b> #{$partida->numero_factura}\n"
            . "📦 <b>Artículo:</b> {$itemName}\n"
            . "👤 <b>Cliente:</b> {$partida->client_name}\n"
            . "💵 <b>Monto:</b> $" . number_format($partida->total, 2) . "\n"
            . "👤 <b>Registrada por:</b> " . Auth::user()->name;
        \App\Services\TelegramService::sendMessage($telegramMessage);

        return redirect()->route('billing')->with('success', 'Factura registrada con éxito.')->with('billing_ids', [$partida->id]);
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
        $data = Billing::findOrFail($id);
        return inertia('Bill/Return', [
            'bill' => $data,
        ]);
    }

    /**
     * Procesa la solicitud de devolución (Total, Temporal/Garantía o Desincorporación),
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

        // 1. Determine new status for inventory
        $newStatus = 'DISPONIBLE';
        $actionVerb = 'DEVOLUCIÓN TOTAL';

        if ($returnType === 'TEMPORAL') {
            $newStatus = 'GARANTIA';
            $actionVerb = 'DEVOLUCIÓN POR GARANTÍA';
        } elseif ($returnType === 'DESINCORPORACION') {
            $newStatus = 'DESINCORPORADO';
            $actionVerb = 'DESINCORPORACIÓN';
        }

        // 2. Update Inventario
        if ($inventario) {
            $inventario->update(['status' => $newStatus]);

            // If it is a temporal return, automatically create a maintenance ticket
            if ($returnType === 'TEMPORAL') {
                \App\Models\Maintenance::create([
                    'fecha' => now()->format('Y-m-d'),
                    'descripcion' => 'DEVOLUCIÓN TEMPORAL POR GARANTÍA. FACTURA ORIGINAL: #' . ($billing->numero_factura ?? 'S/N'),
                    'tipo' => 'GARANTÍA',
                    'status' => 'EN ESPERA',
                    'partida_id' => $inventario->id,
                    'cedula_mecanico' => 0,
                    'nombre_mecanico' => 'POR',
                    'apellido_mecanico' => 'ASIGNAR',
                    'observaciones' => 'Creado automáticamente tras devolución temporal de la factura #' . ($billing->numero_factura ?? 'S/N') . '.',
                ]);
            }
        }

        // 3. Register Reverse Bill
        ReverseBill::create([
            'users_id' => Auth::user()->id,
            'numero_factura' => $request->input('numero_factura') ?? $billing->numero_factura ?? 'S/N',
            'numero_control' => $request->input('numero_control') ?? $billing->numero_control ?? 'S/N',
            'numero_nota_credito' => $request->input('numero_nota_credito') ?? 'S/N',
            'numero_factura_afect' => $request->input('numero_factura_afect') ?? $billing->numero_factura_afect ?? $billing->numero_factura ?? 'S/N',
        ]);

        // 4. Bitácora Entry
        $descLog = mb_strtoupper("{$actionVerb} DE FACTURA: {$billing->numero_factura}. " .
            "NOTA CRÉDITO: " . $request->input('numero_nota_credito') . ". " .
            "ESTADO DE ITEM (#{$inventario->id}): {$newStatus}");

        Bitacora::create([
            'users_id' => Auth::user()->id,
            'action' => mb_strtoupper($actionVerb),
            'description' => $descLog,
        ]);

        // 5. Mark Billing as ANULADA (This automatically discounts from dashboard sales while keeping the history)
        if ($returnType !== 'TEMPORAL') {
            $billing->update(['status' => 'ANULADA']);
        }

        // Notify via Telegram Group
        $itemName = $inventario ? "{$inventario->marca} {$inventario->modelo}" : 'Ítem';
        $notaCredito = $request->input('numero_nota_credito') ?? 'S/N';
        $telegramMessage = "⚠️ <b>Registro de Devolución</b>\n\n"
            . "⚙️ <b>Tipo:</b> {$actionVerb}\n"
            . "📄 <b>Factura Afectada:</b> #{$billing->numero_factura}\n"
            . "🧾 <b>Nota de Crédito:</b> #{$notaCredito}\n"
            . "📦 <b>Motor:</b> {$itemName}\n"
            . "👤 <b>Procesado por:</b> " . Auth::user()->name;
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
}