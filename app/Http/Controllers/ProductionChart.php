<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Batch;

class ProductionChart extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function classificationByMonth()
    {
        try {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();

            $classifications = DB::table('batch_details')
                ->join('categories', 'batch_details.category_id', '=', 'categories.id')
                ->select('categories.categoryName as classification', DB::raw('SUM(batch_details.totalClassification) as total'))
                ->whereBetween('batch_details.created_at', [$startOfMonth, $endOfMonth])
                ->groupBy('categories.categoryName')
                ->get();

            return response()->json([
                'labels' => $classifications->pluck('classification'),
                'data' => $classifications->pluck('total')
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function productionByMonth()
{
    // Agrupar por mes y sumar el total de huevos recolectados
    $production = Batch::select(
        DB::raw('MONTH(created_at) as month'),
        DB::raw('SUM(totalBatch) as total')
    )
    ->groupBy(DB::raw('MONTH(created_at)'))
    ->orderBy(DB::raw('MONTH(created_at)'))
    ->get();

    // Traducir número del mes a nombre
    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
        4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
        10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];

    $labels = [];
    $data = [];

    foreach ($production as $item) {
        $labels[] = $months[$item->month] ?? 'Desconocido';
        $data[] = (int) $item->total;
    }

    return response()->json([
        'labels' => $labels,
        'data' => $data,
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
        //
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
