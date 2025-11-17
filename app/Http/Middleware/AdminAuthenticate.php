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
        // Redirect to the admin login route if not authenticated
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
        // We want to force this middleware to always check the 'admin' guard
        if ($this->auth->guard('admin')->check()) {
            return $this->auth->shouldUse('admin');
        }

        $this->unauthenticated($request, ['admin']);
    }
}
