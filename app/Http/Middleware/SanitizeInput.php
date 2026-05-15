<?php

namespace App\Http\Middleware;

use Closure;

class SanitizeInput
{
    public function handle($request, Closure $next)
    {
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {

            if (!is_string($value)) {
                return;
            }
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = str_replace("\0", '', $value);
            $value = strip_tags($value);
            $value = preg_replace('/javascript:/i', '', $value);
            $value = preg_replace('/on\w+="[^"]*"/i', '', $value);
            $value = trim($value);
        });

        $request->merge($input);

        return $next($request);
    }
}
