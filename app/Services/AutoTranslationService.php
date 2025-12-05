<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    /**
     * Iterates through data and auto-translates missing fields for supported locales.
     *
     * @param array $data
     * @return array
     */
    public function prepareProductData(array $data): array
    {
        $targetLocales = ['fr', 'ar'];
        $translatableFields = ['name', 'description', 'dimensions', 'warranty'];
        $complexFields = ['specifications']; // Fields that are arrays of objects

        // 1. Handle Standard String Fields
        foreach ($translatableFields as $field) {
            // Skip if the source (English) is missing
            if (!isset($data[$field]['en'])) {
                continue;
            }

            foreach ($targetLocales as $locale) {
                // Only translate if the target is empty
                if (empty($data[$field][$locale])) {
                    // Use the helper function (gtrans_html or gtrans)
                    $data[$field][$locale] = gtrans_html($data[$field]['en'], $locale);
                }
            }
        }

        // 2. Handle Complex Array Fields (Specifications)
        // Expected Structure: specifications['en'] = [ ['title' => 'Screen', 'value' => 'OLED'], ... ]
        foreach ($complexFields as $field) {
            if (!isset($data[$field]['en']) || !is_array($data[$field]['en'])) {
                continue;
            }

            foreach ($targetLocales as $locale) {
                // Initialize target array if missing
                if (!isset($data[$field][$locale])) {
                    $data[$field][$locale] = [];
                }

                foreach ($data[$field]['en'] as $index => $row) {
                    $title = $row['title'] ?? '';
                    $value = $row['value'] ?? '';

                    // Translate Title
                    if (empty($data[$field][$locale][$index]['title'])) {
                        $data[$field][$locale][$index]['title'] = $title ? gtrans($title, $locale) : '';
                    }

                    // Translate Value
                    if (empty($data[$field][$locale][$index]['value'])) {
                        $data[$field][$locale][$index]['value'] = $value ? gtrans($value, $locale) : '';
                    }
                }

                // Re-index array to ensure it saves as a valid JSON list (not an object with numeric keys)
                $data[$field][$locale] = array_values($data[$field][$locale]);
            }
        }

        return $data;
    }
}
