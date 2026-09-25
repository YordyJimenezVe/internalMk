<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;
use App\Models\Users;

/**
 * Controlador para la visualización de la Bitácora de Auditoría del sistema.
 * 
 * Permite buscar y paginar registros de bitácoras (historial de acciones realizadas
 * por los usuarios) ordenados cronológicamente de forma descendente.
 */
class BitacorasController extends Controller
{
    /**
     * Muestra el listado de bitácoras filtrado por búsqueda.
     *
     * @param  \Illuminate\Http\Request  $request  Petición HTTP con el término de búsqueda.
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $query = Bitacora::query()
            ->select('bitacoras.*')
            ->leftJoin('users', 'bitacoras.users_id', '=', 'users.id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('bitacoras.action', 'like', "%{$search}%")
                    ->orWhere('bitacoras.description', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        if ($sort === 'user_name') {
            $query->orderBy('users.name', $direction);
        } else {
            $allowedSorts = ['id', 'action', 'description', 'created_at'];
            $sortBy = in_array($sort, $allowedSorts) ? 'bitacoras.' . $sort : 'bitacoras.created_at';
            $query->orderBy($sortBy, $direction);
        }

        $bitacoras = $query->with('users')
            ->paginate(15)
            ->withQueryString();

        return inertia('Bitacora/Index', [
            'bitacoras' => $bitacoras,
            'filters' => $request->only(['search', 'sort', 'direction'])
        ]);
    }
}
