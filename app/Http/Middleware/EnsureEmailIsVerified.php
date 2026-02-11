<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and email is not verified
        if ($request->user() && !$request->user()->email_verified_at) {
            // Don't redirect if already on verification page
            if ($request->route()->getName() !== 'verification.pending') {
                return redirect()->route('verification.pending')->with('warning', 'Please verify your email address to continue.');
            }
        }

        return $next($request);
    }
}
