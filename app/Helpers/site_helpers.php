<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('get_site_setting')) {
    function get_site_setting($key = null, $default = null)
    {
        $settings = Cache::remember('site_settings', 3600, function () {
            return \App\Models\SiteSetting::first();
        });

        if ($key === null) {
            return $settings;
        }

        return $settings ? ($settings->$key ?? $default) : $default;
    }
}

if (!function_exists('is_site_active')) {
    function is_site_active()
    {
        return get_site_setting('site_status') === 'active';
    }
}

if (!function_exists('clear_site_settings_cache')) {
    function clear_site_settings_cache()
    {
        Cache::forget('site_settings');
        Cache::forget('site_status');
    }
}
