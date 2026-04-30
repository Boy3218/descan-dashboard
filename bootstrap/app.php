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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e) {
            echo "<h1>ERROR ASLI:</h1><pre>" . $e->getMessage() . "\n" . $e->getFile() . " on line " . $e->getLine() . "\n\n" . $e->getTraceAsString() . "</pre>";
            exit;
        });
    })->create();

if (isset($_ENV['VERCEL_URL']) || getenv('VERCEL_URL') || getenv('VERCEL')) {
    $app->useStoragePath('/tmp/storage');
}

return $app;
