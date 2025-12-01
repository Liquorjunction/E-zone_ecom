<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\TranslationRepositoryInterface;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function translateAll(string $text, array $locales, bool $isHtml = false): array
    {
        $translations = [];

        foreach ($locales as $locale) {
            // Use HTML-aware translation if needed
            $translations[$locale] = $isHtml ? gtrans_html($text, $locale) : gtrans($text, $locale);
        }

        return $translations;
    }

    public function translateBatch(array $fields, array $locales): array
    {
        $results = [];
        foreach ($fields as $key => $text) {
            if (!empty($text)) {
                // Detect if field is HTML (e.g., 'content' fields contain HTML)
                $isHtml = strpos($key, 'content') !== false;
                $results[$key] = $this->translateAll($text, $locales, $isHtml);
            }
        }
        return $results;
    }
}
