@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-farm-cream via-green-50 to-orange-50 bg-pattern flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-4 h-4 bg-farm-yellow opacity-20 rounded-full animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute top-40 right-20 w-3 h-3 bg-farm-orange opacity-30 rounded-full animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-32 left-20 w-2 h-2 bg-farm-light-green opacity-25 rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-5 h-5 bg-farm-green opacity-15 rounded-full animate-float" style="animation-delay: 1.5s;"></div>
    </div>
    
    {{-- Contenedor principal corregido --}}
    <div class="max-w-7xl w-full mx-auto animate-fade-in md:flex md:space-x-8 items-start">
        <div class="w-full md:w-1/2 space-y-8 md:space-y-0">
            @if(session('success'))
                <div id="success-alert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p class="font-bold">¡Chévere!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(isset($dailyBatch))
                <form method="POST" action="{{ route('harvester.store') }}" class="space-y-6">
                    @csrf
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-white/20">
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-bold text-gray-900">Registrar Recolección</h3>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label for="batch_id" class="block text-sm font-medium text-gray-700 mb-2">Lote del Día</label>
                                <div class="w-full border-2 border-farm-green rounded-xl px-4 py-2 bg-farm-cream text-gray-900 font-bold">
                                    {{ $dailyBatch->batchName }}
                                </div>
                                <input type="hidden" name="batch_id" value="{{ $dailyBatch->id }}">
                            </div>

                            <div>
                                <label for="farm_id" class="block text-sm font-medium text-gray-700 mb-2">Granja de Origen</label>
                                <select id="farm_id" name="farm_id" class="w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green">
                                    <option value="">Seleccione una granja</option>
                                    @foreach($farms as $farm)
                                        <option value="{{ $farm->id }}">{{ $farm->farmName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="trayQuantity" class="block text-sm font-medium text-gray-700 mb-2">Bandejas</label>
                                    <input type="number" id="trayQuantity" name="trayQuantity" min="0" class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green" placeholder="Ej: 10">
                                </div>
                                <div>
                                    <label for="eggUnits" class="block text-sm font-medium text-gray-700 mb-2">Unidades</label>
                                    <input type="number" id="eggUnits" name="eggUnits" min="0" max="29" class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green" placeholder="Ej: 3">
                                    <p class="text-xs text-farm-brown mt-1">*Cada bandeja consta de 30 unidades</p>
                                </div>
                            </div>
                            
                            <div>
                                <label for="totalEggs" class="block text-sm font-medium text-gray-700 mb-2">Total Huevos</label>
                                <input type="number" id="totalEggs" name="totalEggs" readonly class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green bg-gray-100 cursor-not-allowed">
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="bg-gradient-to-r from-farm-orange to-farm-yellow hover:from-farm-orange hover:to-farm-light-green text-white font-bold py-2 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                                    Registrar Recolección
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @else
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 animate-fade-in">
                    <p class="font-bold mb-2">¡Atención!</p>
                    <p>No se ha creado un lote para el día de hoy. Por favor, informa al administrador. No es posible registrar recolecciones.</p>
                </div>
            @endif
        </div>

        <div class="w-full md:w-1/2 mt-8 md:mt-0">
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-white/20">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Últimas 10 Recolecciones</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Granja</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentHarvests as $harvest)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $harvest->created_at->format('d/M H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $harvest->farm->farmName }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-bold">
                                        {{ number_format($harvest->totalEggs) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        ¡Aún no hay recolecciones registradas!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/calculate_eggs.js') }}"></script>
</div>
@endsection