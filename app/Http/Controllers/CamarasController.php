<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;

/**
 * Controlador para la gestión y visualización de Cámaras en el inventario.
 * 
 * Permite filtrar, buscar y paginar registros de tipo CÁMARA disponibles,
 * conectando el flujo de datos directamente con Inertia.
 */
class CamarasController extends Controller
{
    /**
     * Muestra el listado de cámaras filtrado, ordenado y paginado.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros de búsqueda y paginación.
     * @return \Inertia\Response
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
     * Muestra el formulario para registrar una nueva cámara.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una nueva cámara en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición con los datos de creación.
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra los detalles de una cámara específica.
     *
     * @param  string  $id  Identificador único de la cámara.
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario de edición de una cámara.
     *
     * @param  string  $id  Identificador único de la cámara.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza una cámara específica en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición con los datos modificados.
     * @param  string  $id  Identificador único de la cámara.
     * @return void
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Elimina una cámara específica de la base de datos.
     *
     * @param  string  $id  Identificador único de la cámara.
     * @return void
     */
    public function destroy(string $id)
    {
        //
    }
}
