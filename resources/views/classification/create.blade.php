@extends('layouts.classifier')
@section('header', 'Nueva clasificación')

@section('content')
    {{-- ================== FILTRO FECHA + LOTE ================== --}}
    <div class="mb-6">
        <form id="filterForm" method="GET" action="{{ route('classification.create') }}"
            class="flex flex-col sm:flex-row gap-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input id="day" type="date" name="day" value="{{ $day ?? '' }}"
                    class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lote</label>
                <select id="batch_id" name="batch_id"
                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    {{ $day ? '' : 'disabled' }}>
                    <option value="">-- seleccionar --</option>
                    @foreach ($batches as $b)
                        <option value="{{ $b->id }}"
                            {{ (string) $selectedBatch === (string) $b->id ? 'selected' : '' }}>
                            {{ $b->batchName }} (ID: {{ $b->id }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:self-end">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900">
                    Buscar lotes
                </button>
            </div>
        </form>

        <p class="mt-2 text-sm text-gray-500">Selecciona la fecha, luego el lote a clasificar.</p>
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

    {{-- ================== FORMULARIO (se muestra con lote seleccionado) ================== --}}
    @if ($selectedBatch)
        <form method="POST" action="{{ route('classification.store') }}" id="clsForm" class="space-y-6">
            @csrf
            <input type="hidden" name="batch_id" value="{{ $selectedBatch }}">
            <input type="hidden" name="day" value="{{ $day }}">

            {{-- ===== Sticky KPIs (mismo layout del Edit) ===== --}}
            <div class="sticky top-4 z-10">
                <div class="rounded-xl bg-white ring-1 ring-gray-100 shadow p-4">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Total del lote</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums" id="inputQty">
                                {{ $inputQty }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Clasificados</div>
                            <div class="mt-1 text-2xl font-semibold text-gray-900 tabular-nums" id="sumCls">0</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Restantes</div>
                            <div class="mt-1 text-2xl font-semibold tabular-nums" id="balance">{{ $inputQty }}</div>
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

            {{-- ===== Tarjetas por categoría (mismo look) ===== --}}
            <div class="grid md:grid-cols-3 gap-4">
                @foreach ($categories as $c)
                    @php
                        $isRemainder = strtoupper($c->categoryName) === 'YEMAS'; // campo donde autocompleta resto
                    @endphp
                    <div class="bg-white rounded-lg ring-1 ring-gray-100 shadow p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $c->categoryName }}</label>

                        <input type="hidden" name="details[{{ $loop->index }}][category_id]"
                            value="{{ $c->id }}">
                        <input type="number" min="0" name="details[{{ $loop->index }}][totalClassification]"
                            value="{{ old("details.$loop->index.totalClassification", 0) }}"
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
        </form>

        {{-- ===== JS: mismo comportamiento que en Edit ===== --}}
        <script>
            const nf = new Intl.NumberFormat('es-CO');
            const inputs = [...document.querySelectorAll('.cls-input')];
            const sumEl = document.getElementById('sumCls');
            const balEl = document.getElementById('balance');
            const warn = document.getElementById('warn');
            const progress = document.getElementById('progressBar');
            const btnAutof = document.getElementById('btnAutofill');
            const btnReset = document.getElementById('btnReset');
            const eggsPerTray = document.getElementById('eggsPerTray');
            const inputQtyN = parseInt(document.getElementById('inputQty').textContent || '0', 10);
            const autofillInput = document.querySelector('[data-autofill="1"]') || inputs.at(-1);
            const btnSubmit = document.getElementById('btnSubmit');

            // Navegación con Enter / flechas y bloqueo del scroll wheel
            inputs.forEach(i => i.addEventListener('wheel', e => e.preventDefault(), {
                passive: false
            }));
            inputs.forEach((i, idx) => {
                i.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        (inputs[idx + 1] || inputs[0]).focus();
                    }
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        (inputs[idx + 1] || inputs[0]).focus();
                    }
                    if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        (inputs[idx - 1] || inputs.at(-1)).focus();
                    }
                });
            });

            function sumExcept(except) {
                return inputs.reduce((a, el) => a + (el === except ? 0 : parseInt(el.value || '0', 10)), 0);
            }

            function clampToRemaining(target) {
                const others = sumExcept(target);
                const maxAllowed = Math.max(0, inputQtyN - others);
                const val = Math.max(0, parseInt(target.value || '0', 10));
                if (val > maxAllowed) target.value = maxAllowed;
            }

            function recalc(active = null) {
                if (active) clampToRemaining(active);
                let sum = 0;
                inputs.forEach(i => sum += parseInt(i.value || '0', 10));
                const bal = inputQtyN - sum;

                sumEl.textContent = nf.format(sum);
                balEl.textContent = nf.format(bal);

                const pct = inputQtyN > 0 ? Math.min(100, Math.max(0, (sum / inputQtyN) * 100)) : 0;
                progress.style.width = pct + '%';
                progress.className = 'h-2 ' + (bal < 0 ? 'bg-red-600' : (bal === 0 ? 'bg-green-600' : 'bg-yellow-500'));
                warn.classList.toggle('hidden', !(bal < 0));
                btnSubmit.disabled = bal < 0;

                inputs.forEach(i => {
                    const wrap = i.closest('[class*="ring-1"]');
                    const bar = wrap.querySelector('[data-bar]');
                    const pctEl = wrap.querySelector('[data-pct]');
                    const v = parseInt(i.value || '0', 10);
                    const p = inputQtyN > 0 ? Math.min(100, Math.round((v / inputQtyN) * 100)) : 0;
                    bar.style.width = p + '%';
                    pctEl.textContent = p + '%';
                });
            }
            inputs.forEach(i => i.addEventListener('input', () => recalc(i)));
            recalc();

            document.querySelectorAll('.btn-step').forEach(btn => {
                btn.addEventListener('click', () => {
                    const wrap = btn.closest('[class*="ring-1"]');
                    const i = wrap.querySelector('.cls-input');
                    const step = btn.dataset.step;
                    const d = (step === 'tray') ?
                        Math.max(1, parseInt(eggsPerTray.value || '30', 10)) :
                        parseInt(step, 10);
                    i.value = Math.max(0, parseInt(i.value || '0', 10) + d);
                    recalc(i);
                });
            });
            document.querySelectorAll('.btn-max').forEach(btn => {
                btn.addEventListener('click', () => {
                    const wrap = btn.closest('[class*="ring-1"]');
                    const i = wrap.querySelector('.cls-input');
                    const others = sumExcept(i);
                    i.value = Math.max(0, inputQtyN - others);
                    recalc(i);
                });
            });
            btnAutof?.addEventListener('click', () => {
                if (!autofillInput) return;
                const others = sumExcept(autofillInput);
                autofillInput.value = Math.max(0, inputQtyN - others);
                recalc(autofillInput);
            });
            btnReset?.addEventListener('click', () => {
                inputs.forEach(i => i.value = 0);
                recalc();
            });
        </script>
    @endif
@endsection
