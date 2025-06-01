<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))  // Correct use of __DIR__
    ->withRouting(
        web: __DIR__.'/../routes/web.php',    // Correct use of __DIR__
        api: __DIR__.'/../routes/api.php',    // Correct use of __DIR__
        commands: __DIR__.'/../routes/console.php', // Correct use of __DIR__
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'multiauth' => \App\Http\Middleware\MultiAuthMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
