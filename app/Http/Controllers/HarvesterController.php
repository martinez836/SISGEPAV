<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Harvest;

class HarvesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farms = Farm::all();
        return view('harvester.index', compact('farms'));
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
        // validaciones para las entradas del formulario
        $validacion = $request->validate([
            'trayQuantity'=> 'required|numeric|min:1',
            'eggUnits'=> 'required|numeric|min:1|max:30',
            'totalEggs'=> 'required|numeric|min:1',
            'farm_id' => 'required|exists:farms,id',
        ]);
        // guardar en la base de datos el registro
        Harvest::create($validacion);
        return redirect()->route('harvester.index')->with('success', 'Recolección registrada exitosamente');
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
