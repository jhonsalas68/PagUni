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
            // Limpiar cualquier resto de sesión
            session()->flush();
            
            return redirect()->route('login')
                ->with('error', 'Debe iniciar sesión para acceder')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        }

        // Si se especificaron roles, verificar que el usuario tenga uno de ellos
        if (!empty($roles)) {
            $userType = session('user_type');
            if (!in_array($userType, $roles)) {
                return redirect()->route('login')
                    ->with('error', 'No tiene permisos para acceder a esta sección')
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }
        }

        // Agregar headers anti-caché a la respuesta
        $response = $next($request);
        
        return $response
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}