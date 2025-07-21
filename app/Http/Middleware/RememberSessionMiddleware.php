<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class RememberSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado
        if (Auth::check()) {
            // Verificar si tiene una cookie "remember_token"
            $rememberToken = Auth::user()->getRememberToken();
            $cookieToken = Cookie::get(Auth::getRecallerName());
            
            // Si no tiene cookie de recordar, configurar sesión para expirar al cerrar navegador
            if (!$cookieToken || !$rememberToken) {
                config(['session.expire_on_close' => true]);
                $request->session()->put('expire_on_close', true);
            } else {
                // Si tiene cookie, permitir sesión persistente
                config(['session.expire_on_close' => false]);
                $request->session()->put('expire_on_close', false);
            }
        }

        return $next($request);
    }
}
