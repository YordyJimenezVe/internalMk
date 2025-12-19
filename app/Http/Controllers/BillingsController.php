<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;
use App\Models\Partida;
use App\Models\Bitacora;
use App\Models\ReverseBill;
use Illuminate\Support\Facades\Auth;

class BillingsController extends Controller
{

    private function createBitacoraEntry($action,$billingId, $field='', $oldValue='', $newValue='')
    {
        if($action=='UPDATE'){
            $action='UPDATE';
            $descrip="Factura: $billingId, $field: $oldValue, $newValue";
        }else if($action=='DELETE'){
            $action='DELETE';
            $descrip="Factura: $billingId";
        }else if($action=='REVERSE'){
            $action='REVERSE';
            $descrip="Factura: $billingId";
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
        $billings=Billing::with('partidas')->get();
        return inertia('Bill/Index',[
            'Facturas'=>$billings
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $billing=Partida::findOrFail($id)->with('bill')->first();
        // Crear un cliente HTTP
        $client = new \GuzzleHttp\Client();

        // Configurar la solicitud
        $request = $client->request('GET', 'https://pydolarve.org/api/v1/dollar?page=bcv');

        // Decodificar la respuesta JSON
        $response = json_decode($request->getBody()->getContents());
        return inertia(
            'Bill/Create',[
                'data'=>$billing,
                'tasa_bcv'=>$response->monitors->usd->price,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $bs=$request->input('bs');
        $valorD = $request->input('divisa');
        $bsCodificado = str_replace(array(",", "."), "", $bs);
        $iva=16*$bsCodificado/100;
        $partida = new Billing();
        $partida->fill($request->all());
        $valor = number_format($iva);
        $partida->iva = str_replace(',', '.', $valor);
        $partida->divisa = str_replace(',', '.', $valorD);
        $partida->save();

        return redirect()->route('billing');
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
        $campos=$request->all();
        $coleccionA = collect($originalValues);
        $coleccionB = collect($campos);
        $indicesComunes = $coleccionA->intersectByKeys($coleccionB)->keys();
        foreach ($indicesComunes as $indice => $value) {
            $original = $coleccionA[$value];
            $campos = $coleccionB[$value];
            if ($original != $campos) {
              $this->createBitacoraEntry('UPDATE',$billing->numero_factura, $indicesComunes[$indice], 'Valor Original: '.$original, 'Valor Nuevo: '.$campos);
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
        $billing=Billing::findOrFail($id)->with('partidas')->first();
        $marca = $billing->partidas->first()->marca;
        $modelo = $billing->partidas->first()->modelo;
        $billing->delete();
        $this->createBitacoraEntry('DELETE',$billing->numero_factura." ".$marca." ".$modelo);
        return redirect()->route('billing');
    }

    public function return (Billing $partida, $id){
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
        $billing = Billing::findOrFail($id);
        //dd($request);
        ReverseBill::create([
            'users_id' => Auth::user()->id,
            'numero_factura' => $request->input('numero_factura'),
            'numero_control' => $request->input('numero_control'),
            'numero_nota_credito' => $request->input('numero_nota_credito'),
            'numero_factura_afect' => $request->input('numero_factura_afect'),
        ]);
        $this->createBitacoraEntry('REVERSE',$billing->numero_factura." Control n°: ".$billing->numero_control." Nota Crédito n°: ".$request->input('numero_nota_credito')." Factura Afectada n°: ".$request->input('numero_factura_afect'));
        $billing->delete();
        return redirect()->route('billing');
    }
}