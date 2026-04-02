<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Keep admin panel language fixed to English only.
        if ($request->is('admin') || $request->is('admin/*')) {
            App::setLocale('en');

            return $next($request);
        }

        $supportedLocales = ['en', 'fr', 'ar'];
        $defaultLocale = config('app.locale', 'fr');

        $sessionLocale = Session::get('locale');
        $userSelectedLocale = (bool) Session::get('locale_user_selected', false);

        if ($userSelectedLocale && in_array($sessionLocale, $supportedLocales, true)) {
            App::setLocale($sessionLocale);
        } else {
            App::setLocale($defaultLocale);
            Session::put('locale', $defaultLocale);
        }

        return $next($request);
    }
}
