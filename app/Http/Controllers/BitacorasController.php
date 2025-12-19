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
    public function index()
    {
        $bitacoras=Bitacora::with('users')
        ->get();
        return inertia('Bitacora/Index',[
            'bitacoras'=>$bitacoras
        ]);
    }
}
