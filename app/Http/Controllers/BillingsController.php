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
use Carbon\Carbon;

class BillingsController extends Controller
{

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
     * Display a listing of the resource.
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
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $id)
    {
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
                $billing->billing_request_id = $requestId;
            }
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

        return inertia('Bill/Create', [
            'data' => $billing,
            'tasa_bcv' => $tasa,
        ]);
    }

    /**
     * Store a newly created resource in storage.
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
        }

        return redirect()->route('billing')->with('success', 'Factura registrada con éxito.')->with('billing_ids', [$partida->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Billing $bill)
    {
        return inertia('Bill/Edit', [
            'bill' => $bill,
        ]);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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

    public function return(Billing $partida, $id)
    {
        $data = Billing::findOrFail($id);
        return inertia('Bill/Return', [
            'bill' => $data,
        ]);
    }
    /**
     * Update the specified resource in storage.
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
            $newStatus = 'DEVUELTO';
            $actionVerb = 'DEVOLUCIÓN TEMPORAL';
        } elseif ($returnType === 'DESINCORPORACION') {
            $newStatus = 'DESINCORPORADO';
            $actionVerb = 'DESINCORPORACIÓN';
        }

        // 2. Update Inventario
        if ($inventario) {
            $inventario->update(['status' => $newStatus]);
        }

        // 3. Register Reverse Bill
        ReverseBill::create([
            'users_id' => Auth::user()->id,
            'numero_factura' => $request->input('numero_factura'),
            'numero_control' => $request->input('numero_control'),
            'numero_nota_credito' => $request->input('numero_nota_credito'),
            'numero_factura_afect' => $request->input('numero_factura_afect'),
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

        // 5. Delete Billing (This automatically discounts from dashboard sales)
        $billing->delete();

        return redirect()->route('billing')->with('success', "{$actionVerb} procesada con éxito.");
    }
    public function pdf($id)
    {
        $bill = Billing::with('partida')->findOrFail($id);

        $pdf = \PDF::loadView('reports.invoice', compact('bill'));

        // Define paper size and orientation (optional, already set in view or config)
        $pdf->setPaper('letter', 'portrait');

        return $pdf->stream('Factura-' . str_pad($bill->id, 6, '0', STR_PAD_LEFT) . '.pdf');
    }
}