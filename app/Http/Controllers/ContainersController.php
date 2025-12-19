<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Container;

class ContainersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $containers=Container::orderBy('created_at', 'desc')->get();
        return inertia('Container/Index',[
            'containers'=>$containers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Container/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $partida = new Container();
        $partida->fill($request->all());
        $partida->save();
        return redirect()->route('container');
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
    public function edit(Container $partida, $id)
    {
        $data = Container::findOrFail($id);
        return inertia('Container/Edit', [
            'container' => $data,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $container = Container::findOrFail($id);
        $container->fill($request->all());  
        $container->save();

        return redirect()->route('container');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, int $id)
    {
        $container = Container::findOrFail($id);
        $container->delete();
        return redirect()->route('container');
    }
}
