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
        // Handle exceptions for API/JSON requests
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            // Check if request wants JSON response
            if ($request->wantsJson() || $request->is('*/sync-google-sheets')) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e)
                ], 500);
            }
        });
    })->create();
