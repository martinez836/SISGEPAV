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
        <aside
            class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg
                   transform transition-transform duration-200
                   -translate-x-full lg:translate-x-0 z-50">
            <!-- ← clases añadidas para drawer -->
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
        <main class="pl-0 lg:pl-64"><!-- pl-0 explícito: móvil sin sangría, lg compensa sidebar -->
            <header class="bg-white border-b overflow-visible">
                <div class="px-6 h-16 flex items-center justify-between">
                    <div class="flex items-center">
                        <button type="button"
                            class="lg:hidden -ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900"
                            id="sidebar-toggle">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <h1 class="ml-2 lg:ml-0 text-xl font-semibold text-gray-900">Clasificador</h1>
                    </div>

                    <div class="relative z-50">
                        <button type="button"
                            class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                            id="user-menu-button">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">C</span>
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 transform scale-95 opacity-0 transition-all duration-200 ease-out origin-top-right hidden z-50"
                            id="user-dropdown">
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
                                    <button type="submit"
                                        class="flex items-center w-full px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-3 text-red-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <section class="p-6">
                @yield('content')
            </section>
        </main>
    </div>
    <!-- Sidebar overlay for mobile -->
    <div class="fixed inset-0 z-40 lg:hidden bg-gray-600 bg-opacity-75 hidden" id="sidebar-overlay"></div>
</body>

<script>
    (function() {
        const btn = document.getElementById('user-menu-button');
        const menu = document.getElementById('user-dropdown');
        if (!btn || !menu) return;

        let open = false;
        let t;

        function openMenu() {
            if (open) return;
            open = true;
            menu.classList.remove('hidden');
            requestAnimationFrame(() => {
                menu.classList.remove('opacity-0', 'translate-y-1', 'pointer-events-none');
                menu.classList.add('opacity-100', 'translate-y-0');
            });
        }

        function closeMenu() {
            if (!open) return;
            open = false;
            menu.classList.remove('opacity-100', 'translate-y-0');
            menu.classList.add('opacity-0', 'translate-y-1', 'pointer-events-none');
            clearTimeout(t);
            t = setTimeout(() => !open && menu.classList.add('hidden'), 200);
        }

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            open ? closeMenu() : openMenu();
        });

        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeMenu();
        });
    })();

    // === Drawer móvil (responsive) – sin cambiar tu estructura ===
    (function() {
        const aside = document.querySelector('aside');
        const toggle = document.getElementById('sidebar-toggle');
        const overlay = document.getElementById('sidebar-overlay');
        if (!aside || !toggle || !overlay) return;

        const mq = window.matchMedia('(min-width: 1024px)'); // lg

        function openDrawer() {
            aside.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeDrawer() {
            aside.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        function sync(e) {
            if (e.matches) { // lg+
                overlay.classList.add('hidden');
                aside.classList.remove('-translate-x-full');
                document.body.classList.remove('overflow-hidden');
            } else {
                closeDrawer();
            }
        }

        toggle.addEventListener('click', openDrawer);
        overlay.addEventListener('click', closeDrawer);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDrawer();
        });

        // Estado correcto al cargar/redimensionar
        sync(mq);
        mq.addEventListener ? mq.addEventListener('change', sync) : mq.addListener(sync);
    })();
</script>

</html>
