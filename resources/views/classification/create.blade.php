{{-- resources/views/classification/create.blade.php --}}
@extends('layouts.classifier')
@section('header', 'Nueva clasificación')

@section('content')
    <div class="mb-6">
        {{-- FILTRO (Lotes en Recolección, automático) --}}
        <form id="filterForm" method="GET" action="{{ route('classification.create') }}"
            class="mb-6 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-4 items-end">
            <div>
                <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Lote (solo en estado Recolección)
                </label>
                <select id="batch_id" name="batch_id"
                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value=""> seleccionar </option>
                    @foreach ($batches as $b)
                        <option value="{{ $b->id }}"
                            {{ (string) $selectedBatch === (string) $b->id ? 'selected' : '' }}>
                            {{ $b->batchName }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">La lista ya está filtrada automáticamente por estado
                    <b>Recolección</b>.
                </p>
            </div>
        </form>

        <p class="mt-2 text-sm text-gray-500">Selecciona el lote a clasificar.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 text-red-800 px-4 py-3">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ================== FORMULARIO (solo si hay lote seleccionado) ================== --}}
    @if ($selectedBatch)
        <form method="POST" action="{{ route('classification.store') }}" id="clsForm" class="space-y-6">
            @csrf
            <input type="hidden" name="batch_id" value="{{ $selectedBatch }}">

            {{-- ===== KPIs ===== --}}
            <div class="sticky top-4 z-10">
                <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow p-4">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Total del lote</div>
                            <div id="inputQty" class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">
                                {{ (int) ($inputQtyTotal ?? 0) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Clasificados</div>
                            <div id="sumCls" class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">0</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Novedades</div>
                            <div id="sumNov" class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums">0</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Restantes</div>
                            <div id="balance" class="mt-1 text-2xl font-semibold tabular-nums">
                                {{ (int) ($inputQtyTotal ?? 0) }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm text-gray-600 flex items-center gap-2">
                                Huevos por bandeja:
                                <input id="eggsPerTray" type="number" min="1" value="30"
                                    class="w-20 border rounded-md p-1 focus:ring-2 focus:ring-green-500">
                            </label>
                            <button type="button" id="btnReset"
                                class="px-3 py-2 rounded-lg border hover:bg-gray-50">Reset</button>
                            <button type="button" id="btnAutofill"
                                class="px-3 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                                Autocompletar
                            </button>
                            <button type="submit" id="btnSubmit"
                                class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                                Guardar
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div id="progressBar" class="h-2 bg-green-600" style="width: 0%"></div>
                        </div>
                        <div id="warn" class="hidden mt-2 rounded-lg p-2 bg-red-50 text-red-700 text-sm">
                            La suma supera la entrada.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== Categorías ===== --}}
            <div class="grid md:grid-cols-3 gap-4">
                @foreach ($categories as $c)
                    @php $isRemainder = strtoupper($c->categoryName) === 'YEMAS'; @endphp
                    <div class="bg-white rounded-lg ring-1 ring-gray-100 shadow p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $c->categoryName }}</label>

                        <input type="hidden" name="details[{{ $loop->index }}][category_id]"
                            value="{{ $c->id }}">
                        <input type="number" min="0" name="details[{{ $loop->index }}][totalClassification]"
                            value="{{ old("details.$loop->index.totalClassification") }}"
                            class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-green-500 cls-input"
                            {{ $isRemainder ? 'data-autofill=1 id=autofillInput' : '' }}>

                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                            <button type="button" class="btn-step px-2 py-1 rounded border" data-step="-10">−10</button>
                            <button type="button" class="btn-step px-2 py-1 rounded border" data-step="-1">−1</button>
                            <button type="button" class="btn-step px-2 py-1 rounded border" data-step="1">+1</button>
                            <button type="button" class="btn-step px-2 py-1 rounded border" data-step="10">+10</button>
                            <button type="button" class="btn-step px-2 py-1 rounded border" data-step="tray">+1
                                bandeja</button>
                            <button type="button" class="btn-max px-2 py-1 rounded border">Max</button>
                        </div>

                        <div class="mt-3 h-1 w-full bg-gray-100 rounded">
                            <div class="h-1 bg-emerald-500" style="width:0%" data-bar></div>
                        </div>
                        <div class="mt-1 text-xs text-gray-500"><span data-pct>0%</span> del lote</div>
                    </div>
                @endforeach
            </div>

            {{-- ===== Novedades (opcionales, múltiples) ===== --}}
            <div class="bg-white rounded-lg ring-1 ring-gray-100 shadow p-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Novedades</h2>
                    <button type="button" id="btnAddNovelty" class="px-3 py-2 rounded border hover:bg-gray-50">Agregar
                        novedad</button>
                </div>

                @php $oldNov = old('novelties'); @endphp

                <div id="noveltyList" class="mt-4 space-y-3">
                    @if (is_array($oldNov))
                        @foreach ($oldNov as $i => $n)
                            <div class="grid grid-cols-1 md:grid-cols-[140px_1fr_auto] gap-3 items-start novelty-item">
                                <div>
                                    <label class="block text-xs text-gray-600">Cantidad</label>
                                    <input type="number" min="0" name="novelties[{{ $i }}][quantity]"
                                        value="{{ (int) ($n['quantity'] ?? 0) }}" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Descripción</label>
                                    <input type="text" name="novelties[{{ $i }}][novelty]"
                                        value="{{ $n['novelty'] ?? '' }}" class="w-full border rounded p-2">
                                </div>
                                <button type="button"
                                    class="btn-del-nov px-3 py-2 rounded border text-red-700">Eliminar</button>
                            </div>
                        @endforeach
                    @elseif(!empty($novelties) && count($novelties))
                        @foreach ($novelties as $i => $n)
                            <div class="grid grid-cols-1 md:grid-cols-[140px_1fr_auto] gap-3 items-start novelty-item">
                                <div>
                                    <label class="block text-xs text-gray-600">Cantidad</label>
                                    <input type="number" min="0" name="novelties[{{ $i }}][quantity]"
                                        value="{{ (int) $n->quantity }}" class="w-full border rounded p-2">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600">Descripción</label>
                                    <input type="text" name="novelties[{{ $i }}][novelty]"
                                        value="{{ $n->novelty }}" class="w-full border rounded p-2">
                                </div>
                                <button type="button"
                                    class="btn-del-nov px-3 py-2 rounded border text-red-700">Eliminar</button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <template id="noveltyTpl">
                    <div class="grid grid-cols-1 md:grid-cols-[140px_1fr_auto] gap-3 items-start novelty-item">
                        <div>
                            <label class="block text-xs text-gray-600">Cantidad</label>
                            <input type="number" min="0" name="__name__[quantity]"
                                class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600">Descripción</label>
                            <input type="text" name="__name__[novelty]" class="w-full border rounded p-2">
                        </div>
                        <button type="button" class="btn-del-nov px-3 py-2 rounded border text-red-700">Eliminar</button>
                    </div>
                </template>
            </div>
        </form>
    @endif

    {{-- ================== SCRIPTS ================== --}}
    <script>
        // Auto-submit del selector de lote
        (function() {
            const form = document.getElementById('filterForm');
            const lot = document.getElementById('batch_id');
            lot?.addEventListener('change', () => {
                form.requestSubmit ? form.requestSubmit() : form.submit();
            });
        })();

        // ===== Lógica de clasificación (descuenta Novedades) =====
        const nf = new Intl.NumberFormat('es-CO');

        // Clasificación
        const inputs = [...document.querySelectorAll('.cls-input')];
        const sumEl = document.getElementById('sumCls');

        // Novedades
        const novQtyInputs = () => [...document.querySelectorAll('#noveltyList input[name$="[quantity]"]')];
        const sumNovEl = document.getElementById('sumNov');

        // KPIs y progreso general
        const balEl = document.getElementById('balance');
        const warn = document.getElementById('warn');
        const progress = document.getElementById('progressBar');
        const eggsPerTray = document.getElementById('eggsPerTray');
        const btnAutof = document.getElementById('btnAutofill');
        const btnReset = document.getElementById('btnReset');
        const inputQtyN = parseInt(document.getElementById('inputQty')?.textContent || '0', 10);
        const autofillInput = document.querySelector('[data-autofill="1"]') || inputs.at(-1);

        // Utilidades
        inputs.forEach(i => i.addEventListener('wheel', e => e.preventDefault(), {
            passive: false
        }));
        inputs.forEach((i, idx) => {
            i.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    (inputs[idx + 1] || inputs[0]).focus();
                }
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    (inputs[idx - 1] || inputs.at(-1)).focus();
                }
            });
        });

        function sumClassExcept(except) {
            return inputs.reduce((a, el) => a + (el === except ? 0 : parseInt(el.value || '0', 10)), 0);
        }

        function sumNovelties() {
            return novQtyInputs().reduce((a, el) => a + Math.max(0, parseInt(el.value || '0', 10)), 0);
        }

        function clampToRemaining(target) {
            const others = sumClassExcept(target);
            const nov = sumNovelties();
            const maxAllowed = Math.max(0, inputQtyN - (others + nov));
            const val = Math.max(0, parseInt(target.value || '0', 10));
            if (val > maxAllowed) target.value = maxAllowed;
        }

        function recalc(active = null) {
            if (active) clampToRemaining(active);

            const sumCls = inputs.reduce((a, i) => a + parseInt(i.value || '0', 10), 0);
            const sumNov = sumNovelties();
            const used = sumCls + sumNov;
            const bal = inputQtyN - used;

            // KPIs
            sumEl.textContent = nf.format(sumCls);
            sumNovEl.textContent = nf.format(sumNov);
            balEl.textContent = nf.format(bal);

            // Progreso general (clasif + novedades)
            const pct = inputQtyN > 0 ? Math.min(100, Math.max(0, (used / inputQtyN) * 100)) : 0;
            progress.style.width = pct + '%';
            progress.className = 'h-2 ' + (bal < 0 ? 'bg-red-600' : (bal === 0 ? 'bg-green-600' : 'bg-yellow-500'));
            warn?.classList.toggle('hidden', !(bal < 0));

            // Barras por categoría
            inputs.forEach(i => {
                const wrap = i.closest('[class*="ring-1"]');
                const bar = wrap.querySelector('[data-bar]');
                const pctEl = wrap.querySelector('[data-pct]');
                const v = parseInt(i.value || '0', 10);
                const p = inputQtyN > 0 ? Math.min(100, Math.round((v / inputQtyN) * 100)) : 0;
                if (bar) bar.style.width = p + '%';
                if (pctEl) pctEl.textContent = p + '%';
            });
        }

        // Eventos
        inputs.forEach(i => i.addEventListener('input', () => recalc(i)));
        document.addEventListener('input', (e) => {
            if (e.target.matches('#noveltyList input[name$="[quantity]"]')) recalc();
        });

        // Botones
        document.querySelectorAll('.btn-step').forEach(btn => {
            btn.addEventListener('click', () => {
                const wrap = btn.closest('[class*="ring-1"]');
                const i = wrap.querySelector('.cls-input');
                const step = btn.dataset.step;
                const d = (step === 'tray') ? Math.max(1, parseInt(eggsPerTray?.value || '30', 10)) :
                    parseInt(step, 10);
                i.value = Math.max(0, parseInt(i.value || '0', 10) + d);
                recalc(i);
            });
        });
        document.querySelectorAll('.btn-max').forEach(btn => {
            btn.addEventListener('click', () => {
                const wrap = btn.closest('[class*="ring-1"]');
                const i = wrap.querySelector('.cls-input');
                const others = sumClassExcept(i);
                const nov = sumNovelties();
                i.value = Math.max(0, inputQtyN - (others + nov));
                recalc(i);
            });
        });
        btnAutof?.addEventListener('click', () => {
            if (!autofillInput) return;
            const others = sumClassExcept(autofillInput);
            const nov = sumNovelties();
            autofillInput.value = Math.max(0, inputQtyN - (others + nov));
            recalc(autofillInput);
        });
        btnReset?.addEventListener('click', () => {
            inputs.forEach(i => i.value = 0); // no tocamos novedades
            recalc();
        });

        // ===== Novedades dinámicas (AGREGAR/ELIMINAR) =====
        (function() {
            const list = document.getElementById('noveltyList');
            const tpl = document.getElementById('noveltyTpl');
            const btn = document.getElementById('btnAddNovelty');
            if (!list || !tpl || !btn) return;

            function renumber() {
                [...list.querySelectorAll('.novelty-item')].forEach((row, idx) => {
                    row.querySelectorAll('input').forEach(inp => {
                        if (inp.name.startsWith('__name__')) {
                            inp.name = inp.name.replace('__name__', `novelties[${idx}]`);
                        } else {
                            inp.name = inp.name.replace(/novelties\[\d+\]/, `novelties[${idx}]`);
                        }
                    });
                });
                recalc();
            }

            btn.addEventListener('click', () => {
                const node = document.importNode(tpl.content, true);
                list.appendChild(node);
                renumber();
                list.querySelector('.novelty-item:last-child input[name$="[quantity]"]')?.focus();
            });

            list.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-del-nov')) {
                    e.preventDefault();
                    const row = e.target.closest('.novelty-item');
                    row?.remove();
                    renumber();
                }
            });

            // Normaliza índices iniciales
            renumber();
        })();

        // Inicial
        recalc();
    </script>
    <!-- SweetAlert2 (debe ir antes del resto de scripts que lo usan) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Confirmación al guardar clasificación -->
    <script>
        (function() {
            const form = document.getElementById('clsForm');
            if (!form) return;

            let sending = false;

            form.addEventListener('submit', function(e) {
                if (sending) return;
                e.preventDefault();

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Se aplicarán los cambios a la clasificación del lote.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, aplicar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: true
                }).then(({
                    isConfirmed
                }) => {
                    if (isConfirmed) {
                        sending = true;
                        form.submit();
                    }
                });
            });
        })();
    </script>

    {{-- Toast de éxito tras redirect con ->with('ok', '...') --}}
    @if (session('ok'))
        <script>
            // Asegúrate de que SweetAlert2 ya esté cargado arriba
            Swal.fire({
                title: 'Listo',
                text: @json(session('ok')),
                icon: 'success',
                timer: 2200,
                showConfirmButton: false
            });
        </script>
    @endif
@endsection
