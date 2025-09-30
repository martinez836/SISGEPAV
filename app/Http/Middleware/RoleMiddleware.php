<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Constants\Roles; // importa las variables con los roles
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // verifica el rol del usuario autenticado
        if(!Auth::check()){
            return redirect('/login');
        }

        // obtiene el rol del usuario autenticado por su ID
        $userRolId = Auth::user()->rol_id;

        // privilegio total para el administrador
        if($userRolId === Roles::ADMINISTRADOR){
            return $next($request);
        }

        // verifica si el rol del usuario tiene permiso para acceder a la ruta (recolector, clasificador, vendedor)
        if(in_array($userRolId, array_map('intval', $roles))){
            return $next($request);
        }
        // si no tiene permiso, redirige a una página de error o a la página principal
        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
