<?php

use App\Exceptions\OtpException;
use App\Exceptions\RoleExceptions;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
           'admin' => \App\Http\Middleware\EnsureAdmin::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function(OtpException $e,Request $request) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ],$e->getCode());
        });

        $exceptions->renderable(function(RoleExceptions $e,Request $request) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage()
            ],$e->getCode());
        });
    })->create();