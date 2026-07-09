<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'personagem' => App\Http\Middleware\ExigirPersonagem::class,
            'mestre' => App\Http\Middleware\ExigirMestre::class,
        ]);

        $middleware->redirectGuestsTo(fn (): string => route('login'));
        $middleware->redirectUsersTo(fn (): string => route('mapa'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // O padrão do skeleton é `$request->is('api/*')`, e os endpoints de turno
        // vivem sob /batalha. Com aquela regra, uma sessão expirada devolveria uma
        // página HTML de erro no meio de um fetch(), e o batalha.js quebraria ao
        // tentar interpretá-la como JSON. Quem pede JSON recebe JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request): bool => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
