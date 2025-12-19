<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Partida;
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
    public function index()
    {
        $maintenances=Maintenance::with('partida')->get();
        return inertia('Maintenance/Index',[
            'maintenances'=>$maintenances
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        $datas=Partida::all();
        return inertia('Maintenance/Create',[
            'datas'=>$datas,
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Maintenance $maintenance, $id)
    {
        $maintenance = Maintenance::with('bills')->find($id);
        $partida = $maintenance->partida;
        $bill = $maintenance->bills->first() ?? 'N/A';
        $materials = $maintenance->materials->first() ?? 'N/A';
        $accesorios = $maintenance->accesorios_engine->first() ?? 'N/A';
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
            'multi_tools' => $data['multi_tools'],
            'multi_equipament' => $data['multi_equipament'],
            'mechanic' => $data['mechanic'],
            'mechanic_assistant' => $data['mechanic_assistant'],
            'seller' => $data['seller'],
            'seller_assistant' => $data['seller_assistant'],
            'cleaning' => $data['cleaning'],
            'drinking_water' => $data['drinking_water'],
            'consumables' => $data['consumables'],
            'camera_technician' => $data['camera_technician'],
            'camera_technical_assistant' => $data['camera_technical_assistant'],
            'forklift_driver' => $data['forklift_driver']
        ];
        // Separar datos específicos para Materiales
        $extraDataMaterials = [
            'maintenances_id' => $id, 
            'concha_biela' => $data['concha_biela'],
            'concha_bancada' => $data['concha_bancada'],
            'anillos' => $data['anillos'],
            'empacadura_camara' => $data['empacadura_camara'],
            'empacadura_carter' => $data['empacadura_carter'],
            'kit_empacaduras' => $data['kit_empacaduras'],
            'baño_quimico' => $data['baño_quimico'],
            'goma_valvula' => $data['goma_valvula'],
            'planos' => $data['planos'],
            'valvulas' => $data['valvulas'],
            'rectificadora' => $data['rectificadora'],
            'asientos' => $data['asientos'],
            'camisas' => $data['camisas'],
            'levas' => $data['levas'],
            'pistones' => $data['pistones']
        ];
        $maintenance = Maintenance::findOrFail($id);
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

        return redirect()->route('maintenance');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAccesorios(Request $request, int $id)
    {
        $accesorios = AccesorioEngine::firstOrCreate(['maintenances_id' => $id]);
        $accesorios->fill($request->all());  
        $accesorios->save();  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->delete();
        return redirect()->route('maintenance');
    }

    public function getPartida(Request $request)
    {
            $inputPartida=$request->input('partida');
            $inputEmployee=$request->input('employee');
            $partida = Partida::find($inputPartida);
            $employee = Employee::where('cedula',$inputEmployee)->first();
            $datas=Partida::all();
            return Inertia::render('Maintenance/Create', [
                'partidas' => $partida,
                'employee' => $employee,
                'datas' => $datas,
            ]);
    }
}
