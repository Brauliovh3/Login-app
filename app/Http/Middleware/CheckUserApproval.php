<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Si el usuario no tiene el campo status o no está aprobado
            // Para usuarios antiguos sin status, permitir acceso
            if (isset($user->status) && $user->status !== 'approved') {
                auth()->logout();
                
                $message = match($user->status) {
                    'pending' => 'Tu cuenta está pendiente de aprobación por un administrador.',
                    'rejected' => 'Tu solicitud de registro fue rechazada. Contacta al administrador.',
                    default => 'Tu cuenta no tiene acceso autorizado.'
                };
                
                return redirect()->route('login')->withErrors(['login' => $message]);
            }
        }
        
        return $next($request);
    }
}
