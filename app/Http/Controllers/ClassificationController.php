<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBatchClassificationRequest;
use App\Http\Requests\UpdateBatchClassificationRequest;
use App\Models\Batch;
use App\Models\BatchDetail;
use App\Models\Category;
use App\Models\Harvest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ClassificationController extends Controller
{
    /**
     * Listado de lotes con totales calculados por subconsultas.
     */
    public function index()
    {
        $harvested = Harvest::selectRaw('COALESCE(SUM(totalEggs),0)')
            ->whereColumn('batch_id', 'batches.id');

        $classified = BatchDetail::selectRaw('COALESCE(SUM(totalClassification),0)')
            ->whereColumn('batch_id', 'batches.id');

        $batches = Batch::query()
            ->select('batches.*')
            ->selectSub($harvested,  'harvested_sum')
            ->selectSub($classified, 'classified_sum')
            ->orderByDesc('id')
            ->paginate(15);

        return view('classification.index', compact('batches'));
    }

    /**
     * Form de creación. Permite seleccionar lote y ver entrada (suma de harvests).
     */
    public function create(Request $request)
{
    $dayParam = $request->query('day'); // yyyy-mm-dd
    $day = null;

    // Validar/parsear fecha (si viene mal, la ignoramos)
    if ($dayParam) {
        try {
            $day = Carbon::parse($dayParam)->format('Y-m-d');
        } catch (\Throwable $e) {
            $day = null;
        }
    }

    // Lotes a mostrar: SOLO los que tengan recolecciones ese día.
    // Si no hay fecha, NO mostramos lotes (evitamos traer todo).
    if ($day) {
        $batches = Batch::query()
            ->whereIn('id', function ($q) use ($day) {
                $q->select('batch_id')
                  ->from('harvests')
                  ->whereDate('created_at', $day)
                  ->groupBy('batch_id');
            })
            ->orderByDesc('id')
            ->get(['id','batchName']);
    } else {
        $batches = collect(); // vacío hasta que el usuario elija fecha
    }

    $categories    = $this->orderedCategories(); // tu helper ya definido
    $selectedBatch = $request->query('batch_id');

    // Totales
    $inputQtyTotal = 0; // total de huevos del lote (todas las fechas)
    $inputQtyDay   = 0; // total de huevos del lote SOLO en la fecha elegida

    if ($selectedBatch) {
        // total acumulado del lote
        $inputQtyTotal = (int) Harvest::where('batch_id', $selectedBatch)->sum('totalEggs');

        // total del día
        if ($day) {
            $inputQtyDay = (int) Harvest::where('batch_id', $selectedBatch)
                ->whereDate('created_at', $day)
                ->sum('totalEggs');
        }
    }

    // NOTA: la clasificación sigue siendo por lote (acumulada).
    // Usamos la fecha solo para facilitar la selección del lote.
    return view('classification.create', [
        'batches'       => $batches,
        'categories'    => $categories,
        'selectedBatch' => $selectedBatch,
        'day'           => $day,
        'inputQty'      => $inputQtyTotal, // <- tu variable original que usa la vista
        'inputQtyDay'   => $inputQtyDay,   // <- adicional para mostrar "Entrada del día"
    ]);
}

    /**
     * Guarda la clasificación de un lote.
     */
    public function store(StoreBatchClassificationRequest $request)
    {
        DB::transaction(function () use ($request) {

            $batch    = Batch::findOrFail($request->batch_id);
            $inputQty = $this->inputQtyForBatch($batch);

            // Normalizar: agrupa por category_id y suma cantidades (evita duplicados en el POST)
            $rows = collect($request->input('details', []))
                ->map(function ($r) {
                    return [
                        'category_id' => (int) ($r['category_id'] ?? 0),
                        'qty'         => max(0, (int) ($r['totalClassification'] ?? 0)),
                    ];
                })
                ->filter(fn($r) => $r['category_id'] > 0)
                ->groupBy('category_id')
                ->map(fn($g) => [
                    'category_id' => $g->first()['category_id'],
                    'qty'         => (int) $g->sum('qty'),
                ])
                ->values();

            $sum = (int) $rows->sum('qty');
            if ($sum > $inputQty) {
                throw ValidationException::withMessages([
                    'details' => "La suma por categorías ($sum) supera la entrada del lote ($inputQty).",
                ]);
            }

            // Upsert por categoría (crea/actualiza)
            foreach ($rows as $row) {
                BatchDetail::updateOrCreate(
                    ['batch_id' => $batch->id, 'category_id' => $row['category_id']],
                    ['totalClassification' => $row['qty']]
                );
            }
        });

        // Redirección inteligente (opcional): si envías go_to=show desde el form
        return $request->get('go_to') === 'show'
            ? redirect()->route('classification.show', $request->batch_id)->with('ok', 'Clasificación guardada.')
            : redirect()->route('classification.index')->with('ok', 'Clasificación guardada.');
    }

    /**
     * Form de edición de un lote clasificado.
     */
    public function edit($batchId)
    {
        $batch      = Batch::with(['details.category'])->findOrFail($batchId);
        $categories = $this->orderedCategories();
        $inputQty   = $this->inputQtyForBatch($batch);

        return view('classification.edit', compact('batch','categories','inputQty'));
    }

    /**
     * Actualiza y sincroniza las líneas de clasificación del lote.
     * - Valida no exceder entrada
     * - Upsert de categorías enviadas
     * - Elimina categorías NO enviadas (sync real)
     */
    public function update(UpdateBatchClassificationRequest $request, $batchId)
    {
        DB::transaction(function () use ($request, $batchId) {

            $batch    = Batch::findOrFail($batchId);
            $inputQty = $this->inputQtyForBatch($batch);

            $rows = collect($request->input('details', []))
                ->map(function ($r) {
                    return [
                        'category_id' => (int) ($r['category_id'] ?? 0),
                        'qty'         => max(0, (int) ($r['totalClassification'] ?? 0)),
                    ];
                })
                ->filter(fn($r) => $r['category_id'] > 0)
                ->groupBy('category_id')
                ->map(fn($g) => [
                    'category_id' => $g->first()['category_id'],
                    'qty'         => (int) $g->sum('qty'),
                ])
                ->values();

            $sum = (int) $rows->sum('qty');
            if ($sum > $inputQty) {
                throw ValidationException::withMessages([
                    'details' => "La suma por categorías ($sum) supera la entrada del lote ($inputQty).",
                ]);
            }

            // Sync: elimina lo que NO venga en el request
            $keepIds = $rows->pluck('category_id')->all();
            BatchDetail::where('batch_id', $batch->id)
                ->when(!empty($keepIds), fn($q) => $q->whereNotIn('category_id', $keepIds))
                ->delete();

            // Upsert de lo que sí viene
            foreach ($rows as $row) {
                BatchDetail::updateOrCreate(
                    ['batch_id' => $batch->id, 'category_id' => $row['category_id']],
                    ['totalClassification' => $row['qty']]
                );
            }
        });

        return back()->with('ok','Clasificación actualizada.');
    }

    /**
     * Detalle del lote clasificado con KPIs calculados.
     */
    public function show($batchId)
    {
        $batch = Batch::with(['details.category'])->findOrFail($batchId);

        $totalHarvest = $this->inputQtyForBatch($batch);
        $totalClass   = (int) BatchDetail::where('batch_id', $batch->id)->sum('totalClassification');
        $balance      = $totalHarvest - $totalClass;

        // Adjunta atributos para que la vista los lea como $batch->total_harvest_eggs, etc.
        $batch->setAttribute('total_harvest_eggs', $totalHarvest);
        $batch->setAttribute('total_classified',   $totalClass);
        $batch->setAttribute('balance',            $balance);

        return view('classification.show', compact('batch'));
    }

    /**
     * Elimina TODA la clasificación del lote (no borra el lote).
     */
    public function destroy($batchId)
    {
        BatchDetail::where('batch_id', $batchId)->delete();
        return back()->with('ok','Clasificación del lote eliminada.');
    }


    /**
     * Ordena categorías en orden lógico: AAA, AA, A, SUPER, YEMAS.
     */
    private function orderedCategories()
    {
        $order = ['AAA' => 1, 'AA' => 2, 'A' => 3, 'SUPER' => 4, 'YEMAS' => 5];

        return Category::get(['id','categoryName'])
            ->sortBy(function ($c) use ($order) {
                return $order[strtoupper($c->categoryName)] ?? 99;
            })
            ->values();
    }

    /**
     * Total de huevos (entrada) para un batch.
     */
    private function inputQtyForBatch(Batch $batch): int
    {
        return (int) Harvest::where('batch_id', $batch->id)->sum('totalEggs');
    }

    private function inputQtyForBatchId(int $batchId): int
    {
        return (int) Harvest::where('batch_id', $batchId)->sum('totalEggs');
    }
}
