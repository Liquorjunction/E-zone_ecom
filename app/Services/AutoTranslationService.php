<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    protected $locales = ['fr', 'ar']; // Target locales
    protected $tr;
    protected $enabled = true; // Enabled by default

    public function __construct()
    {
        $this->tr = new GoogleTranslate();
    }

    /**
     * Disable translation (useful for bulk imports to improve speed)
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Enable translation
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    public function prepareProductData(array $data): array
    {
        // Skip translation if disabled (for performance during import)
        if (!$this->enabled) {
            // Copy English to other locales without translation for faster import
            $fields = ['name', 'description', 'warranty', 'dimensions'];
            foreach ($fields as $field) {
                if (isset($data[$field]['en']) && !empty($data[$field]['en'])) {
                    foreach ($this->locales as $locale) {
                        if (empty($data[$field][$locale])) {
                            $data[$field][$locale] = $data[$field]['en'];
                        }
                    }
                }
            }

            // Handle specifications
            if (isset($data['specifications']['en']) && is_array($data['specifications']['en'])) {
                foreach ($this->locales as $locale) {
                    if (empty($data['specifications'][$locale])) {
                        $data['specifications'][$locale] = $data['specifications']['en'];
                    }
                }
            }

            return $data;
        }

        // 1. Simple Text Fields (with translation)
        $fields = ['name', 'description', 'warranty', 'dimensions'];

        foreach ($fields as $field) {
            if (isset($data[$field]['en']) && !empty($data[$field]['en'])) {
                $englishValue = $data[$field]['en'];
                $originalEnglishValue = $data[$field]['en'];

                foreach ($this->locales as $locale) {
                    // Only translate if target is empty
                    if (empty($data[$field][$locale])) {
                        $translatedValue = $this->translate($englishValue, $locale);
                        $data[$field][$locale] = $translatedValue;
                    }
                }

                // Restore English value after translation
                $data[$field]['en'] = $originalEnglishValue;
            }
        }

        // 2. Complex JSON (Specifications)
        if (isset($data['specifications']['en']) && is_array($data['specifications']['en'])) {
            foreach ($this->locales as $locale) {
                $translatedSpecs = [];

                foreach ($data['specifications']['en'] as $idx => $spec) {
                    $translatedTitle = $this->translate($spec['title'] ?? '', $locale);
                    $translatedValue = $this->translate($spec['value'] ?? '', $locale);

                    $translatedSpecs[] = [
                        'title' => $translatedTitle,
                        'value' => $translatedValue,
                    ];
                }

                // Save ONLY if the user didn't manually provide this locale
                if (empty($data['specifications'][$locale])) {
                    $data['specifications'][$locale] = $translatedSpecs;
                }
            }
        } else {
            Log::info("No English specifications found");
        }

        // 3. Variants (variant_name and attributes)
        Log::info("Processing variants");
        if (!empty($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $idx => $variant) {
                // Normalize incoming shapes: allow 'name' or 'variant_name'; allow strings
                if (!isset($variant['variant_name'])) {
                    if (isset($variant['name'])) {
                        $variant['variant_name'] = $variant['name'];
                    }
                }

                if (isset($variant['variant_name']) && !is_array($variant['variant_name'])) {
                    $variant['variant_name'] = ['en' => $variant['variant_name']];
                }

                // If name.* exists but variant_name.* missing, copy locales over
                if (isset($variant['name']) && is_array($variant['name'])) {
                    foreach ($variant['name'] as $localeKey => $value) {
                        if (!isset($variant['variant_name'][$localeKey])) {
                            $variant['variant_name'][$localeKey] = $value;
                        }
                    }
                }

                if (empty($variant['variant_name']['en'])) {
                    Log::info("Variant {$idx} has no English name, skipping translation");
                    $data['variants'][$idx] = $variant;
                    continue;
                }

                $originalEnglish = $variant['variant_name']['en'];
                foreach ($this->locales as $locale) {
                    if (empty($variant['variant_name'][$locale])) {
                        $translated = $this->translate($originalEnglish, $locale);
                        $variant['variant_name'][$locale] = $translated;
                        Log::info("Variant {$idx} translated to {$locale}");
                    }
                }

                // Preserve English after translation
                $variant['variant_name']['en'] = $originalEnglish;

                // --- Translate attributes (minor specs) ---
                if (isset($variant['attributes']) && is_array($variant['attributes'])) {
                    // If already translatable structure, skip
                    $isTranslatable = false;
                    foreach ($variant['attributes'] as $key => $val) {
                        if (in_array($key, ['en', 'fr', 'ar'])) {
                            $isTranslatable = true;
                            break;
                        }
                    }
                    if (! $isTranslatable) {
                        // Wrap as English and translate
                        $variant['attributes'] = ['en' => $variant['attributes']];
                    }
                    // Ensure the English attributes are an array. The form sometimes submits JSON strings.
                    $originalAttr = $variant['attributes']['en'] ?? [];
                    if (is_string($originalAttr)) {
                        $decoded = json_decode($originalAttr, true);
                        if (is_array($decoded)) {
                            $originalAttr = $decoded;
                        } else {
                            // If not JSON, keep as single value under a default key
                            $originalAttr = ['value' => $originalAttr];
                        }
                    }
                    foreach ($this->locales as $locale) {
                        if (empty($variant['attributes'][$locale])) {
                            $translatedAttr = [];
                            foreach ($originalAttr as $attrKey => $attrVal) {
                                // Ensure attribute value is string before translating
                                $valToTranslate = is_string($attrVal) ? $attrVal : json_encode($attrVal);
                                $translatedAttr[$attrKey] = $this->translate($valToTranslate, $locale);
                            }
                            $variant['attributes'][$locale] = $translatedAttr;
                        }
                    }
                    // Preserve English after translation
                    $variant['attributes']['en'] = $originalAttr;
                }

                // Keep normalized structure for downstream consumers
                $data['variants'][$idx] = $variant;
            }
        } else {
            Log::info("No variants found for translation");
        }

        Log::info("=== AutoTranslationService.prepareProductData END ===");
        Log::info("Final data structure after translation: " . json_encode(array_keys($data)));
        return $data;
    }

    private function translate(string $text, string $target): string
    {

        try {
            $this->tr->setSource('en');
            $this->tr->setTarget($target);

            $result = $this->tr->translate($text);


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
