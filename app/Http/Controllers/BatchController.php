<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Batch;
use Carbon\Carbon;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = Batch::all();
        return view('admin.batches', compact('batches'));
    }

    public function getBatches()
    {
        $batches = Batch::with('batchState')->get();
        $batches = $batches->map(function($batch) {
            return [
                'id' => $batch->id,
                'batchName' => $batch->batchName,
                'totalBatch' => $batch->totalBatch,
                'batch_state' => $batch->batchState ? $batch->batchState->state : '',
                'created_at' => $batch->created_at,
            ];
        });

        return response()->json($batches);
    }

    public function getBatchToday()
    {
          // Traer la suma de huevos del lote de hoy
        $totalHuevos = Batch::whereDate('created_at', Carbon::today())
                            ->sum('totalBatch'); // <-- suponiendo que totalBatch guarda el número de huevos

        return response()->json([
            'totalHuevos' => $totalHuevos
        ]);
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
            'batchName' => 'required|string|max:255|unique:batches',

            'batch_state_id' => 'required|exists:states,id',
        ]);

        Batch::create([
            'batchName' => $request->batchName,
            'batch_state_id' => $request->batch_state_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
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
        $batches = Batch::find($id);
         if (!$batches) {
            return response()->json(['message' => 'Batch not found'], 404);
        }

        $request->validate([
            'batchName' => 'required|string|max:255|unique:batches,batchName,'.$id,
        ]);

        $batches->batchName = $request->batchName;
        $batches->save();
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }

    public function markCollection(Request $request, string $id)
    {
        $batch = Batch::find($id);

        if (!$batch) {
            return response()->json(['message' => 'Batch not found'], 404);
        }

        $batch->batch_state_id = 2;
        $batch->save();
        return response()->json([
            'success' => true,
            'message' => 'Batch marked as collected successfully'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
