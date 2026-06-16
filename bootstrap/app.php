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
    ->withMiddleware(function (Middleware $middleware) {
        // Form dari project PHP native belum punya token @csrf.
        // Selama proses migrasi bertahap, route lama dikecualikan dulu.
        $middleware->validateCsrfTokens(except: [
            'login.php',
            'register.php',
            'contact.php',
            'detail.php',
            'dashboard-admin.php',
            'edit.php',
            'edit_program.php',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
