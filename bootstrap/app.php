<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CheckSiteStatus;
use App\Http\Middleware\ClientAuthenticate;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        then: function (): void {
            require base_path('routes/admin.php');
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin.auth' => AdminAuthenticate::class,
            'client.auth' => ClientAuthenticate::class,
            'verified.email' => EnsureEmailIsVerified::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            CheckSiteStatus::class,
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            // Keep custom admin error pages only for production with debug disabled.
            if (! app()->environment('production') || config('app.debug')) {
                return null;
            }

            // Skip custom error page for validation exceptions
            if ($exception instanceof \Illuminate\Validation\ValidationException) {
                return null;
            }

            if (! $request->is('admin') && ! $request->is('admin/*')) {
                return null;
            }

            $statusCode = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $supportedErrorPages = [401, 403, 404, 419, 429, 500, 503];

            if (! in_array($statusCode, $supportedErrorPages, true)) {
                $statusCode = 500;
            }

            return response()->view("admin.errors.{$statusCode}", [
                'exception' => $exception,
            ], $statusCode);
        });
    })->create();
