<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partida;

class HistorysController extends Controller
{
    public function index()
    {
        //$partidas=Partida::all();
        $partidas=Partida::with('container')
        ->get();
        return inertia('History/Index',[
            'partidas'=>$partidas
        ]);
    }
}
