<?php

namespace App\Repositories\Admin\Repositories;

use App\Repositories\Admin\Interfaces\TranslationRepositoryInterface;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function translateAll(string $text, array $locales): array
    {
        $translations = [];

        foreach ($locales as $locale) {
            // Uses your existing helper function
            $translations[$locale] = gtrans($text, $locale);
        }

        return $translations;
    }

    public function translateBatch(array $fields, array $locales): array
    {
        $results = [];
        foreach ($fields as $key => $text) {
            if (!empty($text)) {
                $results[$key] = $this->translateAll($text, $locales);
            }
        }
        return $results;
    }
}
