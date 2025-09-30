<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AvicolaManager - Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/login.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-farm-cream via-green-50 to-orange-50 bg-pattern">
    <!-- Background decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <!-- Floating feathers -->
        <div class="absolute top-20 left-10 w-4 h-4 bg-farm-yellow opacity-20 rounded-full animate-float" style="animation-delay: 0s;"></div>
        <div class="absolute top-40 right-20 w-3 h-3 bg-farm-orange opacity-30 rounded-full animate-float" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-32 left-20 w-2 h-2 bg-farm-light-green opacity-25 rounded-full animate-float" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-20 right-10 w-5 h-5 bg-farm-green opacity-15 rounded-full animate-float" style="animation-delay: 1.5s;"></div>
    </div>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 animate-fade-in">
            <!-- Logo and Header -->
            <div class="text-center animate-slide-up">
                <div class="mx-auto h-20 w-20 bg-gradient-to-br mb-10">
                    <!-- Chicken/Egg Icon -->
                    <img src="/images/Logo.jpg" alt="Logo SISGEPAV" class="rounded-full shadow-lg">
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    <span class="text-farm-green">SISGE</span><span class="text-farm-orange">PAV</span>
                </h2>
                <p class="text-gray-600 text-sm">Sistema de Gestión de Producción Avícola</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Login Form -->
            <form class="mt-8 space-y-6 animate-slide-up" style="animation-delay: 0.2s;" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl p-8 border border-white/20">
                    <div class="space-y-6">
                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Email') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input id="email" name="email" type="email" :value="old('email')" required autofocus autocomplete="username"
                                       class="block w-full pl-10 pr-3 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-gray-300"
                                       placeholder="usuario@ejemplo.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-sm text-red-600" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Contraseña') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input id="password" name="password" type="password" required autocomplete="current-password"
                                       class="block w-full pl-10 pr-10 py-3 border-2 border-gray-200 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-farm-green focus:border-transparent transition-all duration-200 hover:border-gray-300"
                                       placeholder="••••••••">
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg id="eye-open" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg id="eye-closed" class="h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-sm text-red-600" />
                        </div>

                        <!-- Remember Me and Forgot Password -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-farm-green focus:ring-farm-green border-gray-300 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                                    {{ __('Recuerdame') }}
                                </label>
                            </div>

                            <div class="text-sm">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="font-medium text-farm-green hover:text-farm-light-green transition-colors duration-200">
                                        {{ __('Olvidaste tu contraseña?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8">
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-farm-green to-farm-light-green hover:from-farm-green hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-farm-green transform hover:scale-[1.02] transition-all duration-200 shadow-lg hover:shadow-xl">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-green-300 group-hover:text-green-200 transition-colors duration-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                            {{ __('Ingresar') }}
                        </button>
                    </div>
                </div>
            </form>

            <!-- Footer -->
            <div class="text-center animate-slide-up" style="animation-delay: 0.4s;">
                <p class="text-xs text-gray-500">
                    © 2025 SISGEPAV. Sistema desarrollado para la gestión integral de granjas avícolas.
                </p>
                <div class="mt-2 flex justify-center space-x-4 text-xs text-gray-400">
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Seguro
                    </span>
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                        </svg>
                        Rápido
                    </span>
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Confiable
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>