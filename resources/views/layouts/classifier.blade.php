<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'SISGEPAV - Clasificación')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg">
            <div class="h-20 border-b flex items-center px-4">
                <img src="/images/Logo.jpg" class="w-10 h-10 rounded-full" alt="">
                <div class="ml-3">
                    <p class="font-bold text-green-700">SISGEPAV</p>
                    <p class="text-xs text-gray-500">Clasificación</p>
                </div>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('classification.index') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('classification.index') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }}">
                    Listado
                </a>
                <a href="{{ route('classification.create') }}"
                    class="block px-4 py-2 rounded-lg {{ request()->routeIs('classification.create') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }}">
                    Nueva clasificación
                </a>
            </nav>
            <div class="absolute bottom-0 w-full p-4 border-t text-sm text-gray-600">
                {{ auth()->user()->name ?? 'Clasificador' }}
            </div>
        </aside>

        <!-- Main -->
        <main class="lg:pl-64">
            <header class="bg-white border-b">
                <div class="px-6 h-16 flex items-center justify-between">
                    <h1 class="text-lg font-semibold text-gray-900">@yield('header', 'Módulo de Clasificación')</h1>
                    <form action="{{ route('logout') }}" method="POST">@csrf
                        <button class="text-red-600 hover:text-red-700 text-sm">Cerrar sesión</button>
                    </form>
                </div>
            </header>

            <section class="p-6">
                @yield('content')
            </section>
        </main>
    </div>
</body>

</html>
