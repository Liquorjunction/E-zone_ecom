<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class FlashToJson
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only transform for AJAX/JSON requests
        if ($request->expectsJson()) {
            // Look for common flash keys
            $keys = ['success', 'error', 'info', 'warning'];
            foreach ($keys as $k) {
                if ($request->session()->has($k)) {
                    $msg = $request->session()->get($k);
                    $type = $k === 'success' ? 'success' : ($k === 'error' ? 'error' : ($k === 'warning' ? 'warning' : 'info'));

                    $toast = [
                        'type' => $type,
                        'title' => ucfirst($k),
                        'message' => $msg,
                        'duration' => 4500,
                    ];

                    return response()->json(['toast' => $toast]);
                }
            }

            // If response is a Redirect (most flash uses), but no session flash keys matched,
            // optionally return an empty JSON success to avoid client-side redirect attempts.
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return response()->json(['status' => 'ok']);
            }
        }

        return $response;
    }
}
