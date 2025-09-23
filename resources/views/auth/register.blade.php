@extends('layouts.app')

@section('content')

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'farm-green': '#2d5016',
                    'farm-light-green': '#4ade80',
                    'farm-orange': '#f97316',
                    'farm-yellow': '#fbbf24',
                    'farm-brown': '#92400e',
                    'farm-cream': '#fefce8',
                },
                animation: {
                    'float': 'float 3s ease-in-out infinite',
                    'fade-in': 'fadeIn 0.8s ease-out',
                    'slide-up': 'slideUp 0.6s ease-out',
                }
            }
        }
    }
</script>
<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes slideUp {
        from { transform: translateY(30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .bg-pattern {
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(45, 80, 22, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(74, 222, 128, 0.04) 0%, transparent 50%);
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-farm-cream via-green-50 to-orange-50 bg-pattern flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Background decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-4 h-4 bg-farm-yellow opacity-20 rounded-full animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute top-40 right-20 w-3 h-3 bg-farm-orange opacity-30 rounded-full animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-32 left-20 w-2 h-2 bg-farm-light-green opacity-25 rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-5 h-5 bg-farm-green opacity-15 rounded-full animate-float" style="animation-delay: 1.5s;"></div>
    </div>
    <div class="max-w-lg w-full space-y-8 animate-fade-in">
        <div class="text-center animate-slide-up">
            <div class="mx-auto h-20 w-20 bg-gradient-to-br from-farm-green to-farm-light-green rounded-full flex items-center justify-center shadow-lg mb-6">
                <img src="/favicon.ico" alt="SISGEPAV Logo" class="h-12 w-auto">
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                <span class="text-farm-green">SISGEPAV</span><span class="text-farm-orange"> Recolección</span>
            </h2>
            <p class="text-gray-600 text-sm">Rol: Recolector</p>
        </div>
        <form method="POST" action="{{ route('harvest.store') }}" class="mt-8 space-y-6 animate-slide-up" style="animation-delay: 0.2s;">
            @csrf
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-white/20">
                <div class="space-y-6">
                    <div>
                        <label for="farm" class="block text-sm font-medium text-gray-700 mb-2">Granja de Origen</label>
                        <select id="farm" name="farm" class="w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green">
                            <option value="">Seleccione una granja</option>
                            <option value="Las Marías">Las Marías</option>
                            <!-- Agrega más opciones dinámicamente -->
                        </select>
                    </div>
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Recolección</label>
                        <input type="date" id="date" name="date" class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="trays" class="block text-sm font-medium text-gray-700 mb-2">Bandejas</label>
                            <input type="number" id="trays" name="trays" min="0" class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green" placeholder="Ej: 10">
                        </div>
                        <div>
                            <label for="units" class="block text-sm font-medium text-gray-700 mb-2">Unidades</label>
                            <input type="number" id="units" name="units" min="0" max="29" class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green" placeholder="Ej: 3">
                            <p class="text-xs text-farm-brown mt-1">*Cada bandeja consta de 30 unidades</p>
                        </div>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Observaciones / Novedades</label>
                        <textarea id="notes" name="notes" rows="3" class="block w-full border-2 border-farm-green rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-farm-light-green" placeholder="Ingrese observaciones o novedades"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="bg-gradient-to-r from-farm-orange to-farm-yellow hover:from-farm-orange hover:to-farm-light-green text-white font-bold py-2 px-6 rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">Registrar Recolección</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="text-center animate-slide-up" style="animation-delay: 0.4s;">
            <p class="text-xs text-gray-500">
                © 2025 SISGEPAV. Sistema desarrollado para la gestión integral de granjas avícolas.
            </p>
        </div>
    </div>
</div>
@endsection
