<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'multirole' => \App\Http\Middleware\MultiRoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'remember.session' => \App\Http\Middleware\RememberSessionMiddleware::class,
            'user.approved' => \App\Http\Middleware\CheckUserApproval::class,
        ]);
        
        // Aplicar middleware a todas las rutas web autenticadas
        $middleware->web(append: [
            \App\Http\Middleware\RememberSessionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
