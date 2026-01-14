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

if (!function_exists('gtrans_html')) {
    function gtrans_html($html, $targetLocale = null)
    {
        $locale = $targetLocale ?? App::getLocale();

        // If English or empty, return original
        if ($locale === 'en' || empty($html)) {
            Log::info("Translation Skipped: '$html' -> ($locale)");
            return $html;
        }

        // Create a cache key
        $cacheKey = "trans_html_{$locale}_" . md5($html);

        // Attempt Translation
        return Cache::remember($cacheKey, 60 * 24 * 30, function () use ($html, $locale) {
            try {
                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML('<div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                $xpath = new DOMXPath($dom);
                $textNodes = $xpath->query('//text()');

                foreach ($textNodes as $node) {
                    $text = trim($node->nodeValue);
                    if (!empty($text)) {
                        $translated = gtrans($text, $locale);
                        Log::info("HTML Translation Success: '$text' -> ($locale) -> '$translated'");
                        $node->nodeValue = $translated;
                    }
                }

                $body = $dom->getElementsByTagName('div')->item(0);
                $innerHTML = '';
                foreach ($body->childNodes as $child) {
                    $innerHTML .= $dom->saveHTML($child);
                }

                Log::info("HTML Translation Success: length " . strlen($html) . " -> ($locale)");

                return $innerHTML;

            } catch (\Throwable $e) {
                Log::error("HTML Translation Failed for ($locale): " . $e->getMessage());

                // Return original HTML as fallback
                return $html;
            }
        });
    }
}
