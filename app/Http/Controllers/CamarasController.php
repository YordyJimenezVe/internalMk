<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;

class CamarasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $tipos = Inventario::whereDoesntHave('bill')
            ->selectRaw('
            SUM(CASE WHEN tipo LIKE "%motor%" THEN 1 ELSE 0 END) AS motores,
            SUM(CASE WHEN tipo = "CAJA AUTOMÁTICA" THEN 1 ELSE 0 END) AS cajas_automaticas,
            SUM(CASE WHEN tipo = "AUTOPARTE" THEN 1 ELSE 0 END) AS autopartes
        ')
            ->get();

        $partidas = Inventario::with('container')
            ->where('tipo', 'CÁMARA')
            ->whereDoesntHave('bill');
        if ($search) {
            // Add search conditions for each column you want to search in
            $partidas->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(marca) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(codInv) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }
        $response = $partidas->paginate(15)->appends($request->query());

        return inertia('Inventario/Index', [
            'partidas' => $response,
            "filters" => [
                'search' => $search, // Pass the search query to the view
            ],
            'tipos' => $tipos,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
