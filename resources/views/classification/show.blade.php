@extends('layouts.classifier')
@section('header', 'Detalle de clasificación')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Clasificación — Lote #{{ $batch->id }} · {{ $batch->batchName }}
            </h1>
            <p class="text-sm text-gray-500">Resumen por categoría.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-3 py-2 rounded-lg border hover:bg-gray-50">Imprimir</button>
            <button id="btnCsv" class="px-3 py-2 rounded-lg border hover:bg-gray-50">Exportar CSV</button>
            <a href="{{ route('classification.edit', $batch->id) }}"
                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">Editar</a>
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-lg bg-white ring-1 ring-gray-100 shadow p-4">
            <div class="text-sm text-gray-500">Entrada</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">
                {{ number_format($batch->total_harvest_eggs, 0, ',', '.') }}</div>
        </div>
        <div class="rounded-lg bg-white ring-1 ring-gray-100 shadow p-4">
            <div class="text-sm text-gray-500">Clasificados</div>
            <div class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">
                {{ number_format($batch->total_classified, 0, ',', '.') }}</div>
        </div>
        @php $bal = $batch->balance; @endphp
        <div class="rounded-lg bg-white ring-1 ring-gray-100 shadow p-4">
            <div class="text-sm text-gray-500">Balance</div>
            <div class="mt-1 text-2xl font-semibold tabular-nums {{ $bal < 0 ? 'text-red-600' : 'text-gray-900' }}">
                {{ number_format($bal, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow ring-1 ring-gray-100 overflow-hidden">
        <table class="min-w-full text-sm" id="tblDetails">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3 text-left">Categoría</th>
                    <th class="p-3 text-right">Cantidad</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($batch->details as $d)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">{{ $d->category->categoryName }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format($d->totalClassification, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-4 text-gray-500" colspan="2">Sin datos de clasificación.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('classification.index') }}" class="text-gray-700 hover:underline">Volver al listado</a>
    </div>

    <script>
        document.getElementById('btnCsv')?.addEventListener('click', () => {
            const rows = [
                ['Categoria', 'Cantidad']
            ];
            document.querySelectorAll('#tblDetails tbody tr').forEach(tr => {
                const tds = tr.querySelectorAll('td');
                if (tds.length === 2) {
                    const cat = tds[0].innerText.trim();
                    const qty = tds[1].innerText.replace(/\./g, '').trim();
                    rows.push([cat, qty]);
                }
            });
            const csv = rows.map(r => r.map(v => `"${(v+'').replace(/"/g,'""')}"`).join(',')).join('\n');
            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'clasificacion_lote_{{ $batch->id }}.csv';
            a.click();
        });
    </script>
@endsection
