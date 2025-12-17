<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Chat\UserOnlineStatus;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo actualizar si el usuario está autenticado
        if ($request->session()->has('user_id') && $request->session()->has('user_type')) {
            $userId = $request->session()->get('user_id');
            $userType = $request->session()->get('user_type');
            
            // Mapear tipos de usuario a clases de modelo
            $userTypeClass = match($userType) {
                'administrador' => \App\Models\Administrador::class,
                'profesor' => \App\Models\Profesor::class,
                'estudiante' => \App\Models\Estudiante::class,
                default => null,
            };
            
            if ($userTypeClass) {
                UserOnlineStatus::updateUserStatus($userId, $userTypeClass);
            }
        }

        return $next($request);
    }
}