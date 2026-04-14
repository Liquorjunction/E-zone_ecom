<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class ClientAuthenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    protected function authenticate($request, array $guards)
    {
        $guard = $this->auth->guard('web');
        if ($guard->check()) {
            // When auth is restored from remember-me cookie on mobile resume,
            // rotate session+CSRF once to avoid stale-token 419 bursts.
            if (method_exists($guard, 'viaRemember') && $guard->viaRemember() && !$request->session()->has('remember_session_refreshed')) {
                $request->session()->migrate(true);
                $request->session()->regenerateToken();
                $request->session()->put('remember_session_refreshed', true);
            }

            $user = $guard->user();
            if ($user && !$user->is_active) {
                $guard->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your account has been disabled. Please contact support.',
                        'redirect' => route('login')
                    ], 403);
                }

                abort(redirect()->route('login')->withErrors([
                    'login' => 'Your account has been disabled. Please contact support.'
                ]));
            }
            return $this->auth->shouldUse('web');
        }
        $this->unauthenticated($request, ['web']);
    }
}
