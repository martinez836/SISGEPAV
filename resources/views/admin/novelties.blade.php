@extends('layouts.novelties')

@section('title', 'SISGEPAV - Novedades')

@section('content')
    <div class="min-h-screen bg-gray-50">
        {{-- Sidebar reutilizable --}}
        @include('layouts.sidebar')

        {{-- Contenido principal --}}
        <div class="lg:pl-64">
            {{-- Header superior --}}
            <div class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center">
                            <button type="button"
                                class="lg:hidden -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900"
                                id="sidebar-toggle">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <h1 class="ml-2 lg:ml-0 text-xl font-semibold text-gray-900">Novedades</h1>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="hidden sm:block text-sm text-gray-500" id="current-time">
                                {{ date('d/m/Y H:i') }}
                            </div>

                            <div class="relative">
                            <button type="button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200" id="user-menu-button">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">A</span>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name}}</p>
                                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 transform scale-95 opacity-0 transition-all duration-200 ease-out origin-top-right hidden" id="user-dropdown">
                                <div class="py-1">
                                    <!-- User Info Header -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name}}</p>
                                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                                    </div>

                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <!-- Logout Button -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contenido del módulo --}}
            <main class="p-4 sm:p-6 lg:p-8">
                {{-- Filtros --}}
                <form id="filters" method="GET" action="{{ route('novelties.index') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Fecha</label>
                        <input type="date" name="date" value="{{ $date }}"
                            class="w-full rounded-md border-gray-300 focus:ring-green-600 focus:border-green-600">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Lote (obligatorio)</label>
                        <select name="batch_id" id="batch_id"
                            class="w-full rounded-md border-gray-300 focus:ring-green-600 focus:border-green-600">
                            {{-- Placeholder --}}
                            <option value="" {{ $batchId ? '' : 'selected' }} disabled>— Selecciona un lote —</option>
                            @foreach ($batches as $b)
                                <option value="{{ $b->id }}" @selected($batchId == $b->id)>{{ $b->batchName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <button id="applyBtn" type="submit"
                            class="h-10 w-full md:w-auto px-4 rounded-md font-medium text-white
                                    bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ $batchId ? '' : 'disabled' }}>
                            Aplicar
                        </button>
                    </div>
                </form>

                {{-- Cards superiores --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Recolectados</div>
                        <div class="mt-2 text-3xl font-semibold">{{ number_format($recolectados) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Clasificados</div>
                        <div class="mt-2 text-3xl font-semibold">{{ number_format($clasificados) }}</div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-500">Novedades</div>
                        <div class="mt-2 text-3xl font-semibold">{{ number_format($novedadesTotal) }}</div>
                    </div>
                </div>

                {{-- Cards por categoría --}}
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mt-6">
                    @foreach (['AAA', 'AA', 'A', 'SUPER', 'YEMAS'] as $cat)
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="text-sm text-gray-500">{{ $cat }}</div>
                            <div class="mt-2 text-2xl font-semibold">{{ number_format($categorias[$cat]) }}</div>
                        </div>
                    @endforeach
                </div>

                {{-- Tabla --}}
                <div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-3 text-sm text-gray-600">
                        Novedades en el período: {{ $from->format('d/m/Y 00:00') }} — {{ $to->format('d/m/Y 23:59') }}
                    </div>
                    <div class="overflow-x-auto">
                        <table id="noveltiesTable" class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr class="text-left">
                                    <th class="px-4 py-2 font-medium text-gray-600">FECHA</th>
                                    <th class="px-4 py-2 font-medium text-gray-600">LOTE</th>
                                    <th class="px-4 py-2 font-medium text-gray-600">CANTIDAD</th>
                                    <th class="px-4 py-2 font-medium text-gray-600">DESCRIPCIÓN</th>
                                    <th class="px-4 py-2 font-medium text-gray-600">USUARIO</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($novedades as $n)
                                    <tr>
                                        <td class="px-4 py-2">
                                            {{ $n->created_at->setTimezone('America/Bogota')->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2">{{ $n->batch_code }}</td>
                                        <td class="px-4 py-2 font-semibold">{{ number_format($n->quantity) }}</td>
                                        <td class="px-4 py-2">{{ $n->novelty }}</td>
                                        <td class="px-4 py-2">{{ $n->user_name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if ($novedades->isEmpty())
                                <div class="px-4 py-3 text-center text-sm text-gray-500">
                                    No hay novedades en el rango seleccionado.
                                </div>
                            @endif
                        </table>
                    </div>
                </div>
            </main>
        </div>

        {{-- Overlay  --}}
        <div class="fixed inset-0 z-40 lg:hidden bg-gray-600 bg-opacity-75 hidden" id="sidebar-overlay"></div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // DataTables
            if (window.jQuery && $.fn.DataTable) {
                $('#noveltiesTable').DataTable({
                    pageLength: 10,
                    order: [
                        [0, 'desc']
                    ],
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                        emptyTable: 'No hay novedades en el rango seleccionado.'
                    }
                });
            }

            const form = document.getElementById('filters');
            const dateInput = form.querySelector('input[name="date"]');
            const batchSel = document.getElementById('batch_id');
            const applyBtn = document.getElementById('applyBtn');
            const endpoint = "{{ route('novelties.batchesByDate') }}";

            let inFlight; // para abortar peticiones previas

            const syncApplyState = () => {
                applyBtn.disabled = !batchSel.value;
            };

            const renderOptions = (items) => {
                batchSel.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = '— Selecciona un lote —';
                placeholder.disabled = true;
                placeholder.selected = true;
                batchSel.appendChild(placeholder);

                if (!items || items.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'No hay lotes para esta fecha';
                    opt.disabled = true;
                    batchSel.appendChild(opt);
                    return;
                }

                for (const it of items) {
                    const opt = document.createElement('option');
                    opt.value = it.id;
                    opt.textContent = it.batchName;
                    batchSel.appendChild(opt);
                }
            };

            const fetchBatches = async (dateValue) => {
                if (inFlight) inFlight.abort();
                inFlight = new AbortController();

                batchSel.disabled = true;
                applyBtn.disabled = true;
                renderOptions([]);

                try {
                    const url = endpoint + '?date=' + encodeURIComponent(dateValue);
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: inFlight.signal
                    });
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    const json = await res.json();
                    renderOptions(json.data || []);
                } catch (e) {
                    console.error('Error cargando lotes:', e);
                    renderOptions([]);
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'Error cargando lotes';
                    opt.disabled = true;
                    batchSel.appendChild(opt);
                } finally {
                    batchSel.disabled = false;
                    inFlight = null;
                }
            };

            // Cambiar fecha => repoblar lotes
            dateInput.addEventListener('change', () => {
                fetchBatches(dateInput.value);
            });

            // Habilita/Deshabilita "Aplicar" según elección de lote
            batchSel.addEventListener('change', syncApplyState);

            // Estado inicial
            syncApplyState();
        });
    </script>
@endpush
