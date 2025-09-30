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
use App\Models\BatchState;
use App\Models\Novelty;
use Illuminate\Support\Facades\Auth;

class ClassificationController extends Controller
{
    /**
     * Listado de lotes con totales calculados por subconsultas.
     */
    public function index(Request $request)
    {
        $harvested = Harvest::selectRaw('COALESCE(SUM(totalEggs),0)')
            ->whereColumn('batch_id', 'batches.id');

        $classified = BatchDetail::selectRaw('COALESCE(SUM(totalClassification),0)')
            ->whereColumn('batch_id', 'batches.id');

        $recoleccionId = \App\Models\BatchState::whereRaw('LOWER(state)=?', ['recoleccion'])->value('id');

        $batches = Batch::query()
            ->when($recoleccionId, fn($q)=>$q->where('batch_state_id',$recoleccionId))
            ->select('batches.*')
            ->selectSub($harvested,  'harvested_sum')
            ->selectSub($classified, 'classified_sum')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // 👇 fallback: si no hay harvests, usa totalBatch
        $batches->getCollection()->transform(function ($b) {
            $b->harvested_sum = (int)($b->harvested_sum ?: $b->totalBatch);
            return $b;
        });

        return view('classification.index', compact('batches'));
    }




    /**
     * Form de creación. Permite seleccionar lote y ver entrada (suma de harvests).
     */

    public function create(Request $request)
    {
        $selectedBatch = $request->query('batch_id');

        // Lotes en estado Recolección
        $recoleccionId = BatchState::whereRaw('LOWER(state)=?', ['recoleccion'])->value('id');
        $batches = Batch::query()
            ->when($recoleccionId, fn($q) => $q->where('batch_state_id', $recoleccionId))
            ->orderByDesc('id')
            ->get(['id','batchName','totalBatch']);

        // Total del lote (suma harvests o fallback a totalBatch)
        $inputQtyTotal = $selectedBatch ? $this->inputQtyForBatchId((int)$selectedBatch) : 0;

        // Categorías y detalle existente
        $categories = Category::orderBy('categoryName')->get(['id','categoryName']);
        $existing   = $selectedBatch
            ? BatchDetail::where('batch_id', $selectedBatch)
                ->get(['category_id','totalClassification'])
                ->keyBy('category_id')
            : collect();

        // Novedades por batch_code (batchName) -> NO batch_id
        $novelties = collect();
        if ($selectedBatch) {
            $batch = Batch::select('id','batchName')->find($selectedBatch);
            if ($batch) {
                $novelties = Novelty::where('batch_code', $batch->batchName)
                    ->get(['id','quantity','novelty']);
            }
        }

        return view('classification.create', compact(
            'batches','categories','selectedBatch','inputQtyTotal','existing','novelties'
        ));
    }


    /**
     * Guarda la clasificación de un lote.
     */
    public function store(\App\Http\Requests\StoreBatchClassificationRequest $request)
    {
        DB::transaction(function () use ($request) {
            $batch = Batch::findOrFail($request->batch_id);
            $inputQty = $this->inputQtyForBatch($batch); // harvests o totalBatch

            // --- Clasificaciones (normalizadas) ---
            $rows = collect($request->input('details', []))
                ->map(fn ($r) => [
                    'category_id' => (int)($r['category_id'] ?? 0),
                    'qty'         => max(0, (int)($r['totalClassification'] ?? 0)),
                ])
                ->filter(fn ($r) => $r['category_id'] > 0)
                ->groupBy('category_id')
                ->map(fn ($g) => ['category_id' => $g->first()['category_id'], 'qty' => (int)$g->sum('qty')])
                ->values();

            $sumClass = (int)$rows->sum('qty');

            // --- Novedades desde el request ---
            $reqNovs = collect($request->input('novelties', []))
                ->map(fn ($n) => [
                    'quantity' => max(0, (int)($n['quantity'] ?? 0)),
                    'novelty'  => trim((string)($n['novelty'] ?? '')),
                ])
                ->filter(fn ($n) => $n['quantity'] > 0 || $n['novelty'] !== '')
                ->values();
            $sumNov = (int)$reqNovs->sum('quantity');

            // --- Validación de tope ---
            $used = $sumClass + $sumNov;
            if ($used > $inputQty) {
                throw ValidationException::withMessages([
                    'details' => "La suma de categorías ($sumClass) + novedades ($sumNov) = $used supera la entrada del lote ($inputQty).",
                ]);
            }

            // --- Upsert clasificaciones ---
            foreach ($rows as $row) {
                BatchDetail::updateOrCreate(
                    ['batch_id' => $batch->id, 'category_id' => $row['category_id']],
                    ['totalClassification' => $row['qty']]
                );
            }

            // --- Novedades: borrar y recrear por batch_code ---
            Novelty::where('batch_code', $batch->batchName)->delete();
            $userName = auth()->user()->name ?? 'Sistema';
            foreach ($reqNovs as $n) {
                Novelty::create([
                    'batch_code' => $batch->batchName,
                    'quantity'   => $n['quantity'],
                    'novelty'    => $n['novelty'],
                    'user_name'  => $userName,
                ]);
            }

            $batch->batch_state_id = 3;
            $batch->save();
        });

        return redirect()->route('classification.index')->with('ok', 'Clasificación guardada.');
    }



    /**
     * Form de edición de un lote clasificado.
     */
    public function edit($batchId)
    {
        $batch      = Batch::with(['details.category'])->findOrFail($batchId);
        $categories = $this->orderedCategories();
        $inputQty   = $this->inputQtyForBatch($batch);

        // novedades del lote
        $novelties = Novelty::where('batch_code', $batch->batchName)
            ->get(['id','quantity','novelty']);

        return view('classification.edit', compact('batch','categories','inputQty','novelties'));
    }


    /**
     * Actualiza y sincroniza las líneas de clasificación del lote.
     * - Valida no exceder entrada
     * - Upsert de categorías enviadas
     * - Elimina categorías NO enviadas (sync real)
     */
    public function update(\App\Http\Requests\UpdateBatchClassificationRequest $request, $batchId)
    {
        DB::transaction(function () use ($request, $batchId) {
            $batch    = Batch::findOrFail($batchId);
            $inputQty = $this->inputQtyForBatch($batch);

            // --- Clasificaciones ---
            $rows = collect($request->input('details', []))
                ->map(fn ($r) => [
                    'category_id' => (int)($r['category_id'] ?? 0),
                    'qty'         => max(0, (int)($r['totalClassification'] ?? 0)),
                ])
                ->filter(fn ($r) => $r['category_id'] > 0)
                ->groupBy('category_id')
                ->map(fn ($g) => ['category_id' => $g->first()['category_id'], 'qty' => (int)$g->sum('qty')])
                ->values();

            $sumClass = (int)$rows->sum('qty');

            // --- Novedades del request ---
            $reqNovs = collect($request->input('novelties', []))
                ->map(fn ($n) => [
                    'quantity' => max(0, (int)($n['quantity'] ?? 0)),
                    'novelty'  => trim((string)($n['novelty'] ?? '')),
                ])
                ->filter(fn ($n) => $n['quantity'] > 0 || $n['novelty'] !== '')
                ->values();
            $sumNov = (int)$reqNovs->sum('quantity');

            // --- Validación ---
            $used = $sumClass + $sumNov;
            if ($used > $inputQty) {
                throw ValidationException::withMessages([
                    'details' => "La suma de categorías ($sumClass) + novedades ($sumNov) = $used supera la entrada del lote ($inputQty).",
                ]);
            }

            // --- Sync de detalles ---
            $keepIds = $rows->pluck('category_id')->all();
            BatchDetail::where('batch_id', $batch->id)
                ->when(!empty($keepIds), fn ($q) => $q->whereNotIn('category_id', $keepIds))
                ->delete();

            foreach ($rows as $row) {
                BatchDetail::updateOrCreate(
                    ['batch_id' => $batch->id, 'category_id' => $row['category_id']],
                    ['totalClassification' => $row['qty']]
                );
            }

            // --- Novedades: borrar y recrear por batch_code ---
            Novelty::where('batch_code', $batch->batchName)->delete();
            $userName = auth()->user()->name ?? 'Sistema';
            foreach ($reqNovs as $n) {
                Novelty::create([
                    'batch_code' => $batch->batchName,
                    'quantity'   => $n['quantity'],
                    'novelty'    => $n['novelty'],
                    'user_name'  => $userName,
                ]);
            }

            $batch->batch_state_id = 3;
            $batch->save();
        });

        return redirect()->route('classification.index')->with('ok', 'Clasificación actualizada.');
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

    private function inputQtyForBatch(Batch $batch): int
    {
        $sum = (int) Harvest::where('batch_id', $batch->id)->sum('totalEggs');
        return $sum > 0 ? $sum : (int) ($batch->totalBatch ?? 0);
    }

    private function inputQtyForBatchId(int $batchId): int
    {
        $sum = (int) Harvest::where('batch_id', $batchId)->sum('totalEggs');
        if ($sum > 0) return $sum;
        return (int) (Batch::where('id', $batchId)->value('totalBatch') ?? 0);
    }

    private function stateId(string $name): ?int
    {
        return BatchState::whereRaw('LOWER(state)=?', [strtolower($name)])->value('id');
    }
}