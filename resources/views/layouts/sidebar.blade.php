@php
    $link = fn($active) => $active
        ? 'flex items-center px-4 py-3 text-green-700 bg-green-50 rounded-lg transition-colors duration-200'
        : 'flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-colors duration-200';
@endphp

<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
    id="sidebar">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-20 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <div class="w-10 h-10 rounded-full flex items-center justify-center">
                <img src="/images/Logo.jpg" alt="Logo SISGEPAV" class="rounded-full shadow-lg">
            </div>
            <div>
                <h2 class="text-xl font-bold text-green-700">SISGEPAV</h2>
                <p class="text-xs text-gray-500">Sistema de Gestión Avícola</p>
            </div>
        </div>
    </div>

    {{-- === NAVIGATION === --}}
    @php
        $link = fn($active) => $active
            ? 'flex items-center px-4 py-3 text-green-700 bg-green-50 rounded-lg transition-colors duration-200'
            : 'flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-lg transition-colors duration-200';

        $icon = fn($active) => $active ? 'w-5 h-5 mr-3 text-green-700' : 'w-5 h-5 mr-3 text-gray-500';

        // Helper para evaluar la ruta actual
        $is = fn(string $pattern) => request()->is($pattern);
    @endphp

    <nav class="mt-8">
        <div class="px-4 mt-4 space-y-2">
            {{-- Dashboard --}}
            <a href="/dashboard" class="{{ $link($is('dashboard')) }}">
                <svg class="{{ $icon($is('dashboard')) }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M3 10h10v9H3zM17 9h4v10h-4z" />
                </svg>
                Dashboard
            </a>

            {{-- Usuarios --}}
            <a href="/users" class="{{ $link($is('users') || $is('users/*')) }}">
                <svg class="{{ $icon($is('users') || $is('users/*')) }}" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 14a4 4 0 10-8 0v4h8v-4zM12 9a3 3 0 110-6 3 3 0 010 6z" />
                </svg>
                Usuarios
            </a>

            {{-- Granjas --}}
            <a href="/farms" class="{{ $link($is('farms') || $is('farms/*')) }}">
                <svg class="{{ $icon($is('farms') || $is('farms/*')) }}" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10" />
                </svg>
                Granjas
            </a>

            {{-- Roles --}}
            <a href="/roles" class="{{ $link($is('roles') || $is('roles/*')) }}">
                <svg class="{{ $icon($is('roles') || $is('roles/*')) }}" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10" />
                </svg>
                Roles
            </a>

            {{-- Lotes --}}
            <a href="/batches" class="{{ $link($is('batches') || $is('batches/*')) }}">
                <svg class="{{ $icon($is('batches') || $is('batches/*')) }}" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Lotes
            </a>

            {{-- Novedades --}}
            <a href="/novelties" class="{{ $link($is('novelties') || $is('novelties/*')) }}">
                <svg class="{{ $icon($is('novelties') || $is('novelties/*')) }}" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <!-- Círculo de aviso -->
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Clasificacion
            </a>
        </div>
    </nav>
    {{-- === /NAVIGATION === --}}


    {{-- Usuario --}}
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium">A</span>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</div>
