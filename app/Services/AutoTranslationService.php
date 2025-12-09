<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    protected $locales = ['fr', 'ar']; // Target locales
    protected $tr;

    public function __construct()
    {
        $this->tr = new GoogleTranslate();
    }

    public function prepareProductData(array $data): array
    {
        // 1. Simple Text Fields
        $fields = ['name', 'description', 'warranty', 'dimensions'];

        Log::info("=== AutoTranslationService.prepareProductData START ===");
        Log::info("Input data keys: " . json_encode(array_keys($data)));

        foreach ($fields as $field) {
            Log::debug("Processing field: {$field}");

            if (isset($data[$field])) {
                Log::debug("Field {$field} exists, structure: " . json_encode($data[$field]));
            } else {
                Log::debug("Field {$field} NOT found in data");
                continue;
            }

            if (isset($data[$field]['en']) && !empty($data[$field]['en'])) {
                $englishValue = $data[$field]['en'];
                // Log truncated version for readability (first 100 chars)
                $logValue = strlen($englishValue) > 100 ? substr($englishValue, 0, 100) . '...' : $englishValue;
                Log::info("Field {$field} HAS English value (length: " . strlen($englishValue) . "): '{$logValue}'");

                // CRITICAL: Store original English value to prevent loss during translation
                $originalEnglishValue = $data[$field]['en'];

                foreach ($this->locales as $locale) {
                    Log::debug("Processing locale {$locale} for field {$field}");

                    // Only translate if target is empty
                    if (empty($data[$field][$locale])) {
                        Log::info("Translating field {$field} from EN to {$locale}");
                        $translatedValue = $this->translate($englishValue, $locale);
                        $data[$field][$locale] = $translatedValue;
                        Log::info("Translation SUCCESS - {$field}.{$locale} (length: " . strlen($translatedValue) . ")");
                    } else {
                        Log::info("Skipping translation for {$field}.{$locale} - already has value: '" . $data[$field][$locale] . "'");
                    }
                }

                // CRITICAL FIX: Explicitly restore English value after translation
                // This prevents Spatie Translatable from removing empty/lost English values
                $data[$field]['en'] = $originalEnglishValue;

                // Verify English value is STILL present
                if (isset($data[$field]['en']) && !empty($data[$field]['en'])) {
                    Log::info("Verification: {$field}.en preserved successfully (length: " . strlen($data[$field]['en']) . ")");
                } else {
                    Log::error("CRITICAL ERROR: {$field}.en was LOST after translation! Attempting recovery...");
                    // Attempt recovery
                    $data[$field]['en'] = $originalEnglishValue;
                    Log::warning("Recovery attempted: {$field}.en restored from backup");
                }
            } else {
                Log::debug("Field {$field} - No English value found or is empty");
                // If field exists but is empty array, skip translation to avoid issues
                if (isset($data[$field]) && is_array($data[$field]) && empty($data[$field])) {
                    Log::debug("Field {$field} is empty array - skipping translation");
                }
            }
        }

        // 2. Complex JSON (Specifications)
        // Structure: [['title' => 'Screen', 'value' => 'OLED']]
        Log::debug("Processing specifications");
        if (isset($data['specifications']['en']) && is_array($data['specifications']['en'])) {
            Log::info("Specifications found, count: " . count($data['specifications']['en']));

            foreach ($this->locales as $locale) {
                Log::debug("Translating specifications to {$locale}");
                $translatedSpecs = [];

                foreach ($data['specifications']['en'] as $idx => $spec) {
                    Log::debug("Spec [{$idx}] - title: '" . ($spec['title'] ?? '') . "', value: '" . ($spec['value'] ?? '') . "'");

                    $translatedTitle = $this->translate($spec['title'] ?? '', $locale);
                    $translatedValue = $this->translate($spec['value'] ?? '', $locale);

                    $translatedSpecs[] = [
                        'title' => $translatedTitle,
                        'value' => $translatedValue,
                    ];

                    Log::debug("Spec [{$idx}] translated to {$locale} - title: '{$translatedTitle}', value: '{$translatedValue}'");
                }

                // Save ONLY if the user didn't manually provide this locale
                if (empty($data['specifications'][$locale])) {
                    $data['specifications'][$locale] = $translatedSpecs;
                    Log::info("Specifications saved for locale {$locale}");
                } else {
                    Log::info("Skipping specifications for {$locale} - user provided override");
                }
            }
        } else {
            Log::debug("No English specifications found");
        }

        Log::info("=== AutoTranslationService.prepareProductData END ===");
        Log::debug("Final data structure after translation: " . json_encode(array_keys($data)));

        return $data;
    }

    private function translate(string $text, string $target): string
    {
        if (empty($text)) {
            Log::debug("translate() called with empty text for locale {$target}");
            return '';
        }

        try {
            $this->tr->setSource('en');
            $this->tr->setTarget($target);
            Log::debug("Translating text (length: " . strlen($text) . ") to {$target}");

            $result = $this->tr->translate($text);

            Log::debug("Translation result: '{$result}'");

            if ($result === $text) {
                Log::warning("Translation returned original text (no change detected) for {$target}");
            }

            return $result;
        } catch (\Exception $e) {
            Log::error("Translation FAILED for {$target}: " . $e->getMessage());
            Log::warning("Fallback: Using English text instead");
            return $text; // Fallback to English on failure
        }
    }
}
