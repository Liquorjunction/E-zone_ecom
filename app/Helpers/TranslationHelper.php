<?php

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

if (!function_exists('gtrans')) {
    function gtrans($text, $targetLocale = null)
    {
        $locale = $targetLocale ?? App::getLocale();

        // 1. If English or empty, return original
        if ($locale === 'en' || empty($text)) {
            return $text;
        }

        // 2. Create a cache key
        $cacheKey = "trans_{$locale}_" . md5($text);

        // 3. Attempt Translation
        return Cache::remember($cacheKey, 60 * 24 * 30, function () use ($text, $locale) {
            try {
                // Check if class exists before using it
                if (!class_exists(GoogleTranslate::class)) {
                    throw new \Exception("GoogleTranslate class not found. Run 'composer require stichoza/google-translate-php'");
                }

                $tr = new GoogleTranslate();
                $tr->setSource('en');
                $tr->setTarget($locale);
                $translated = $tr->translate($text);

                // Log Success
                Log::info("Translation Success: '$text' -> ($locale) -> '$translated'");

                return $translated;

            } catch (\Throwable $e) {
                // Log Error so we know what happened
                Log::error("Translation Failed for ($locale): " . $e->getMessage());

                // Return original text as fallback
                return $text;
            }
        });
    }
}
