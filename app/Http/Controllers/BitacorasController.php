<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bitacora;
use App\Models\Users;

class BitacorasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $bitacoras = Bitacora::with('users')
            ->when($search, function ($query, $search) {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('users', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return inertia('Bitacora/Index', [
            'bitacoras' => $bitacoras,
            'filters' => $request->only(['search'])
        ]);
    }
}
