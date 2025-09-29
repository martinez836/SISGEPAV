<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $farms = Farm::all();

        return view('admin.farms',compact('farms'));
    }

    public function getFarms()
    {
        $farms = Farm::with('state')->get();
        
        $farms = $farms->map(function($farm) {
            return [
                'id' => $farm->id,
                'farmName' => $farm->farmName,
                'state_id' => $farm->state ? $farm->state->stateName : '',
            ];
        });

        return response()->json($farms);
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
        $request->validate([
            'farmName' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        Farm::create([
            'farmName' => $request->farmName,
            'state_id' => $request->state_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Farm created successfully',
        ], 201);
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
        $farm = Farm::findOrFail($id);
        if(!$farm) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found',
            ], 404);
        }

        $request->validate([
            'farmName' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $farm->farmName = $request->farmName;
        $farm->state_id = $request->state_id;
        $farm->save();
        return response()->json([
            'success' => true,
            'message' => 'Farm updated successfully',
        ], 200);

    }

    public function deactivate(Request $request, string $id)
    {
        $farm = Farm::findOrFail($id);
        if(!$farm) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found',
            ], 404);
        }

        $farm->state_id = 2;
        $farm->save();
        return response()->json([
            'success' => true,
            'message' => 'Farm deactivated successfully',
        ], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
