<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\SiteSetting;
use Symfony\Component\HttpFoundation\Response;

class CheckSiteStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*')) {
            return $next($request);
        }

        $siteStatus = Cache::remember('site_status', 60, function () {
            $siteSetting = SiteSetting::first();
            return $siteSetting ? $siteSetting->site_status : 'active';
        });

        if ($siteStatus === 'inactive') {
            if ($request->is('maintenance')) {
                return $next($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Site is currently under maintenance. Please check back later.',
                    'status' => 'maintenance'
                ], 503);
            }

            return response()->view('client.errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
