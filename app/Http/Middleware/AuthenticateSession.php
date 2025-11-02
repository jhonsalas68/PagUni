<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verificar si hay sesión activa
        if (!session()->has('user_id') || !session()->has('user_type')) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para acceder');
        }

        // Si se especificaron roles, verificar que el usuario tenga uno de ellos
        if (!empty($roles)) {
            $userType = session('user_type');
            if (!in_array($userType, $roles)) {
                return redirect()->route('login')->with('error', 'No tiene permisos para acceder a esta sección');
            }
        }

        return $next($request);
    }
}