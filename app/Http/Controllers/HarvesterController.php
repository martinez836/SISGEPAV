<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Harvest;
use App\Models\Batch;
use Illuminate\Support\Facades\Auth;

class HarvesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // obtiene todas las granjas
        $farms = Farm::all();
        // obtiene el lote con batchState = 'Nuevo' 
        $stateBatch = Batch::where('batch_state_id', 1)->get();
        // obtiene las últimas 10 recolecciones del usuario autenticado para mostrar en el historial en la vista
        $recentHarvests = Harvest::with('farm')
        ->where('user_id', Auth::id())
        ->latest()
        ->take(7)
        ->get();

        return view('harvester.index', compact('farms', 'stateBatch', 'recentHarvests'));
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
            'trayQuantity'=> 'required|numeric|min:0',
            'eggUnits'=> 'required|numeric|min:0|max:30',
            'farm_id' => 'required|exists:farms,id',
            'batch_id' => 'required|exists:batches,id',
        ]);

        // calcular el total de huevos
        $calculatedTotalEggs = ($validacion['trayQuantity'] * 30) + $validacion['eggUnits'];

        // preparar los datos para guardar en la base de datos
        $recoleccionData = array_merge($validacion, [
            'totalEggs' => $calculatedTotalEggs,
            'user_id' => Auth::id()
        ]);

        // guardar en la base de datos el registro
        Harvest::create($recoleccionData);
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
