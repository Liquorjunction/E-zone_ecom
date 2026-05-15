<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CheckSiteStatus;
use App\Http\Middleware\ClientAuthenticate;
use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        then: function (): void {
            require base_path('routes/admin.php');
        },
        commands: __DIR__ . '/../routes/console.php',
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
            SanitizeInput::class,
            CheckSiteStatus::class,
            SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            if ($exception instanceof TokenMismatchException) {
                $redirectUrl = ($request->is('admin') || $request->is('admin/*'))
                    ? route('admin.login')
                    : route('login');

                Log::warning('CSRF token mismatch detected.', [
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                    'user_id' => optional($request->user('web'))->id,
                    'is_authenticated' => $request->user('web') !== null,
                    'remember_cookie_present' => collect(array_keys($request->cookies->all()))
                        ->contains(fn ($key) => str_starts_with($key, 'remember_web_')),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'expects_json' => $request->expectsJson(),
                ]);

                if ($request->hasSession()) {
                    $request->session()->regenerateToken();
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => statictranslations('Your session has expired. Please sign in again.'),
                        'redirect' => $redirectUrl,
                        'csrf_token' => csrf_token(),
                    ], 419);
                }

                // For regular form submissions (common on mobile resume),
                // recover in place instead of bouncing to login immediately.
                if (! $request->is('admin') && ! $request->is('admin/*')) {
                    return back()->withInput($request->except(['password', 'password_confirmation']))
                        ->with('error', statictranslations('Your session was refreshed. Please submit again.'));
                }

                return redirect()->to($redirectUrl)
                    ->with('error', statictranslations('Your session has expired. Please sign in again.'));
            }

            // Only apply in production with debug disabled
            if (! app()->environment('production') || config('app.debug')) {
                return null;
            }

            // Skip for non-admin routes
            if (! $request->is('admin') && ! $request->is('admin/*')) {
                return null;
            }

            // Skip exceptions that should redirect, not render an error page
            $redirectExceptions = [
                \Illuminate\Validation\ValidationException::class,
                \Illuminate\Auth\AuthenticationException::class,
            ];

            foreach ($redirectExceptions as $class) {
                if ($exception instanceof $class) {
                    return null; // Let Laravel handle redirects normally
                }
            }

            // Only render custom error pages for non-JSON requests
            // (AJAX/API calls should get JSON error responses)
            if ($request->expectsJson()) {
                return null;
            }

            $statusCode = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            $supportedErrorPages = [401, 403, 404, 429, 500, 503];
            // Note: 419 removed — handled via redirect above

            if (! in_array($statusCode, $supportedErrorPages, true)) {
                $statusCode = 500;
            }

            return response()->view("admin.errors.{$statusCode}", [
                'exception' => $exception,
            ], $statusCode);
        });
    })->create();
