<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AdminAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('admin.login');
    }

    /**
     * Get the guard(s) to be used.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function authenticate($request, array $guards)
    {
        $guard = $this->auth->guard('admin');
        if ($guard->check()) {
            $admin = $guard->user();
            if ($admin && $admin->status === 'inactive') {
                $guard->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                abort(redirect()->route('admin.login')->withErrors([
                    'login' => 'Your account has been disabled. Please contact support.'
                ]));
            }
            return $this->auth->shouldUse('admin');
        }
        $this->unauthenticated($request, ['admin']);
    }
}
