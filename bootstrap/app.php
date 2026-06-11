<?php

use App\Domain\Whois\Exceptions\UserFacingException;
use App\Http\Support\ApiErrorResponder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

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
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (TooManyRequestsHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Too many requests. Please wait a moment before trying again.',
                    'code' => 'rate_limited',
                ], 429);
            }
        });

        $exceptions->render(function (UserFacingException $exception, Request $request) {
            return ApiErrorResponder::fromException($exception, $request);
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            return ApiErrorResponder::fromException($exception, $request);
        });
    })->create();
