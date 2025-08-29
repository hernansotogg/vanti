<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AntiBotMiddleware;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware) {
        //$middleware->prepend(AntiBotMiddleware::class);
        $middleware->web(append: [
        HandleInertiaRequests::class,
        ]);
        $middleware->alias([
        'phone.session' => \App\Http\Middleware\EnsurePhoneSession::class,
        ]);
        $middleware->validateCsrfTokens(except: [
        //'/telebot/webhook/pse/*',
        '/telebot/webhook/tarjeta/*',
        '/pago/response',
        '/pago/confirmation',
    ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
