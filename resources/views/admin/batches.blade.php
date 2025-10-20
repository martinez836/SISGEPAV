@extends('layouts.batches')

@section('title', 'SISGEPAV - Dashboard Administrativo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    
    @include('layouts.sidebar')

    <!-- Main Content -->
    <div class="lg:pl-64">
        <!-- Top Header -->
        <div class="bg-white shadow-sm border-b border-gray-200">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <button type="button" class="lg:hidden -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900" id="sidebar-toggle">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="ml-2 lg:ml-0 text-xl font-semibold text-gray-900">Lotes</h1>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div id="current-time" class="hidden sm:block text-sm text-gray-500"></div>
                        
                        <!-- User Dropdown -->
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
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 transform scale-95 opacity-0 transition-all duration-200 ease-out origin-top-right hidden z-50" id="user-dropdown">
                                <div class="py-1">
                                    <!-- User Info Header -->
                                    <div class="px-4 py-3 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
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

        <!-- Dashboard Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Stats Cards -->
            

            <!-- Charts Row -->


            <!-- Latest Lots Table -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Lotes Registrados</h3>
                        <button id="openModalBtn" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-400 text-white rounded-lg shadow-md hover:from-green-700 hover:to-green-500 transition"> Crear Lote </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="batchesTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Id</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Granja</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total de Huevos</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="batchTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay + Modal -->
    <div id="BatchModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg transform transition-all scale-95 opacity-0">
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h2 class="text-xl font-semibold text-green-700" id="modalTitle">Nuevo Lote</h2>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-800">✕</button>
            </div>

            <!-- Cuerpo -->
            <form id="BatchForm">
                <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Codigo de Lote</label>
                    <input type="text" id="batchCode" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                </div>
                <!-- Footer -->
                <div class="flex justify-end gap-3 px-6 py-4 border-t">
                    <button id="cancelModalBtn" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-green-600 to-green-400 text-white shadow hover:from-green-700 hover:to-green-500 transition">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Sidebar overlay for mobile -->
    <div class="fixed inset-0 z-40 lg:hidden bg-gray-600 bg-opacity-75 hidden" id="sidebar-overlay"></div>
</div>

<script>

</script>
@endsection