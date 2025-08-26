<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = strtolower((string) auth()->user()->role);
        $normalizedRoles = array_map('strtolower', $roles);

        if (in_array($userRole, $normalizedRoles)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a esta página.');
    }
}
