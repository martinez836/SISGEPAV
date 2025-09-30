@extends('layouts.batches')

@section('title', 'SISGEPAV - Dashboard Administrativo')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out" id="sidebar">
        <!-- Logo -->
        <div class="flex items-center justify-center h-20 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-r rounded-full flex items-center justify-center">
                    <img src="/images/Logo.jpg" alt="Logo SISGEPAV" class="rounded-full shadow-lg">
                </div>
                <div>
                    <h2 class="text-xl font-bold text-green-700">SISGEPAV</h2>
                    <p class="text-xs text-gray-500">Sistema de Gestión Avícola</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-8">
            <div class="px-4">
                <a href="/dashboard" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    Dashboard
                </a>
            </div>
            
            <div class="px-4 mt-4 space-y-2">
                <a href="/users" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Usuarios
                </a>
                
                <a href="/farms" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H3.862a2 2 0 01-1.995-1.858L1 7m3 4v6m4-6v6m4-6v6M5 7V4a1 1 0 011-1h8a1 1 0 011 1v3M9 4v3h2V4"/>
                    </svg>
                    Granjas
                </a>
                
                <a href="/roles" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                    </svg>
                    Roles
                </a>
                
                <a href="/batches" class="flex items-center px-4 py-3 text-green-700 bg-green-50 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM4 9a2 2 0 100 4h12a2 2 0 100-4H4zM4 15a2 2 0 100 4h12a2 2 0 100-4H4z"/>
                    </svg>
                    Lotes
                </a>
                
                <a href="#" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M16 8v8a2 2 0 01-2 2H6a2 2 0 01-2-2V8m8-4V2a1 1 0 00-1-1H9a1 1 0 00-1 1v2M8 6h4"/>
                    </svg>
                    Gráficas
                </a>
            </div>
        </nav>

        <!-- User info at bottom -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-sm font-medium">A</span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name}}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </div>

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
                                    
                                    <!-- Menu Items -->
                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Mi Perfil
                                    </a>
                                    
                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Configuración
                                    </a>

                                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Ayuda
                                    </a>

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