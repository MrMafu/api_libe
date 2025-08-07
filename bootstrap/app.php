<?php

use App\Http\Middleware\AdminAndLibrarianOnly;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function () {
        return App\Schedule\Kernel::class;
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->alias([
            "admin-and-librarian-only" => AdminAndLibrarianOnly::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

return $app;
