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
