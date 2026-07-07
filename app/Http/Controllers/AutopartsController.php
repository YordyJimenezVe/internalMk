<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventario;

/**
 * Controlador para la gestión y visualización de Autopartes en el inventario.
 * 
 * Permite filtrar, ordenar, buscar y paginar los registros de tipo AUTOPARTE,
 * segregando su estado (disponible / vendido) y conectando con Inertia.
 */
class AutopartsController extends Controller
{
    /**
     * Muestra el listado de autopartes filtrado, ordenado y paginado.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con filtros de búsqueda, estado y orden.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        // Clean barcode scanner mapping (apostrophe to hyphen) immediately
        $searchCleaned = str_replace("'", "-", $search);
        
        $user = auth()->user();
        $isMechanic = $user && $user->hasAnyRole(['MECANICO', 'Tecnico', 'Mecanico', 'TECNICO']);

        // --- Smart Redirect Logic (Global) ---
        if ($searchCleaned) {
            $partida = null;
            if (str_contains($searchCleaned, '-') || (strlen($searchCleaned) >= 4 && !is_numeric($searchCleaned))) {
                // 1. Try exact match on codInv
                $partida = Inventario::where('codInv', $searchCleaned)->first();
                
                // 2. Try matching container code and inventory code (e.g. CXDU-15 -> container CXDU, codInv 15)
                if (!$partida && str_contains($searchCleaned, '-')) {
                    $lastDashPos = strrpos($searchCleaned, '-');
                    $containerPart = substr($searchCleaned, 0, $lastDashPos);
                    $codInvPart = substr($searchCleaned, $lastDashPos + 1);
                    
                    $partida = Inventario::where('codInv', $codInvPart)
                        ->whereHas('container', function($q) use ($containerPart) {
                            $q->where('cod', 'LIKE', $containerPart . '%');
                        })->first();
                        
                    if (!$partida) {
                        $partida = Inventario::where('codInv', $codInvPart)->first();
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

        $tipos = Inventario::whereDoesntHave('bill')
            ->selectRaw('
            SUM(CASE WHEN tipo LIKE "%motor%" THEN 1 ELSE 0 END) AS motores,
            SUM(CASE WHEN tipo = "CAJA AUTOMÁTICA" THEN 1 ELSE 0 END) AS cajas_automaticas,
            SUM(CASE WHEN tipo = "AUTOPARTE" THEN 1 ELSE 0 END) AS autopartes
        ')
            ->get();

        $statusFilter = $request->input('status', 'ALL'); // Default

        $partidas = Inventario::with('container')
            ->where('tipo', 'AUTOPARTE');

        // Status Filter
        if ($statusFilter === 'DISPONIBLE') {
            $partidas->whereDoesntHave('bill')->where('status', '!=', 'VENDIDO');
        } elseif ($statusFilter === 'VENDIDO') {
            $partidas->where(function ($q) {
                $q->has('bill')->orWhere('status', 'VENDIDO');
            });
        }

        if ($search) {
            $codInvSearch = $searchCleaned;
            if (str_contains($searchCleaned, '-')) {
                $parts = explode('-', $searchCleaned);
                $codInvSearch = end($parts);
            }
            // Add search conditions for each column you want to search in
            $partidas->where(function ($query) use ($search, $searchCleaned, $codInvSearch) {
                $query->whereRaw('LOWER(marca) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(modelo) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereRaw('LOWER(codInv) LIKE ?', ['%' . strtolower($codInvSearch) . '%'])
                    ->orWhereRaw('LOWER(codInv) LIKE ?', ['%' . strtolower($searchCleaned) . '%']);
            });
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');
        $partidas->orderBy($sort, $direction);

        $response = $partidas->paginate(15)->appends($request->query());

        return inertia('Inventario/Index', [
            'partidas' => $response,
            "filters" => [
                'search' => $search,
                'status' => $statusFilter,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'tipos' => $tipos,
        ]);
    }

    /**
     * Muestra el formulario para registrar una nueva autoparte.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Almacena una nueva autoparte en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición con los datos del formulario.
     * @return void
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Muestra los detalles de una autoparte específica.
     *
     * @param  string  $id  Identificador único de la autoparte.
     * @return void
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Muestra el formulario de edición de una autoparte.
     *
     * @param  string  $id  Identificador único de la autoparte.
     * @return void
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Actualiza una autoparte específica en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request  Petición con los datos modificados.
     * @param  string  $id  Identificador único de la autoparte.
     * @return void
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Elimina una autoparte específica de la base de datos.
     *
     * @param  string  $id  Identificador único de la autoparte.
     * @return void
     */
    public function destroy(string $id)
    {
        //
    }
}
