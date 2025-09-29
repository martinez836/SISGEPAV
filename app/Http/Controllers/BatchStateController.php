<?php

namespace App\Http\Controllers;

use App\Models\BatchState;
use Illuminate\Http\Request;

class BatchStateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //obtenemos los datos del request con todos los estados
        $batchStates = BatchState::all();
        return response()->json($batchStates);
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
        // Validar y crear un nuevo estado de lote
        $request->validate([
            'state' => 'required|string|max:45',
        ]);
        
        // Crear el nuevo estado de lote y responde solo con JSON

        $batchState = BatchState::create($request->all());
        return response()->json($batchState, 201);
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
