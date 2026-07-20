<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/midtrans-callback'
        ]);
        // Studio pakai null (bukan '') untuk "reset ke default" (lihat komentar di
        // TemplateEditorController@updateSection). Konversi global '' -> null bikin
        // teks yang sengaja dikosongkan ikut ke-reset alih-alih tersimpan kosong.
        $middleware->convertEmptyStringsToNull(except: [
            fn ($request) => $request->is('admin/api/*'),
        ]);
        $middleware->alias([
            'staff' => \App\Http\Middleware\EnsureUserIsStaff::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
