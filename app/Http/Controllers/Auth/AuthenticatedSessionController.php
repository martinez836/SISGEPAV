<?php

namespace App\Http\Controllers\Auth;

use App\Constants\Roles; // importa las variables con los roles
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // llama a la función para redirigir según el rol
        return $this->handleRoleRedirection($request);
    }

    public function handleRoleRedirection(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $rol_id = $user->rol_id;

        // busca la ruta correspondiente al rol del usuario
        $redirectTo = Roles::Role_Routes[$rol_id] ?? '/dashboard';

        // redirige al usuario a la ruta correspondiente
        return redirect()->intended($redirectTo);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
