<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value) {

            if (!is_string($value)) {
                return;
            }
            $value = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $value);
            $value = preg_replace('/javascript:/i', '', $value);
            $value = preg_replace('/on\w+\s*=/i', '', $value);
        });

        $request->merge($input);

        return $next($request);
    }
}
