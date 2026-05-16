<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->email_verified_at) {
            $exemptedRoutes = ['verification.pending', 'email.verify', 'admin.email.verify'];
            if (!in_array($request->route()->getName(), $exemptedRoutes)) {
                return redirect()->route('verification.pending')->with('warning', 'Please verify your email address to continue.');
            }
        }

        return $next($request);
    }
}
