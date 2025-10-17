@extends('layouts.classifier')

@section('header', 'Clasificación por lote')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Clasificación por lote</h1>
            <p class="text-sm text-gray-500">Control de cantidades clasificadas por categoría.</p>
        </div>
    </div>

    @if (session('ok'))
        <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3">{{ session('ok') }}</div>
    @endif

    <div class="bg-white shadow rounded-xl ring-1 ring-gray-100 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="p-3 text-left">#</th>
                    <th class="p-3 text-left">Lote</th>
                    <th class="p-3 text-right">Entrada (recolecciones)</th>
                    <th class="p-3 text-right">Clasificados</th>
                    <th class="p-3 text-right">Novedades</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($batches as $b)
                    @php
                        $bal = (int) $b->harvested_sum - (int) $b->classified_sum;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">{{ $b->id }}</td>
                        <td class="p-3 font-medium text-gray-900">{{ $b->batchName }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format($b->harvested_sum, 0, ',', '.') }}</td>
                        <td class="p-3 text-right tabular-nums">{{ number_format($b->classified_sum, 0, ',', '.') }}</td>
                        <td class="p-3 text-right">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs tabular-nums
                {{ $bal < 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ number_format($bal, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="p-8 text-center text-gray-500" colspan="6">
                            No hay lotes para mostrar.
                            <a href="{{ route('classification.create') }}"
                                class="text-green-700 hover:underline ml-1">Crear clasificación</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $batches->links() }}</div>
@endsection
