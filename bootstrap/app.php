<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, $e) {
            return true;
        });

        $exceptions->render(function (\Throwable $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() ?: 'Internal Server Error',
                'errors' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        });
    })->create();
