<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    protected $locales = ['fr', 'ar']; // Target locales
    protected $tr;
    protected $enabled = true; // Enabled by default
    // Request-scoped translation cache to avoid duplicate calls
    protected $cache = [];

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
                    $needTranslate = false;
                    // Translate when target locale missing or when it's identical to English (frontend may have copied English into other locales)
                    if (empty($variant['variant_name'][$locale])) {
                        $needTranslate = true;
                    } else {
                        if (is_string($variant['variant_name'][$locale]) && $variant['variant_name'][$locale] === $originalEnglish) {
                            $needTranslate = true;
                        }
                    }

                    if ($needTranslate) {
                        $translated = $this->translate($originalEnglish, $locale);
                        $variant['variant_name'][$locale] = $translated;
                        Log::info("Variant {$idx} translated/overwritten to {$locale}");
                    }
                }

                // Preserve English after translation
                $variant['variant_name']['en'] = $originalEnglish;

                // Ensure variant_name values are plain strings (not arrays/objects)
                foreach (['en','fr','ar'] as $ln) {
                    if (isset($variant['variant_name'][$ln]) && is_array($variant['variant_name'][$ln])) {
                        $v = $variant['variant_name'][$ln];
                        // If numeric indexed, prefer first element
                        if (isset($v[0]) && is_string($v[0]) && count($v) === 1) {
                            $variant['variant_name'][$ln] = $v[0];
                        } elseif (isset($v[0]) && is_string($v[0])) {
                            $variant['variant_name'][$ln] = implode(' / ', array_map('strval', $v));
                        } else {
                            // associative array: join values
                            $variant['variant_name'][$ln] = implode(' - ', array_map('strval', array_values($v)));
                        }
                    }
                }

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
                    // Normalize attribute entries: ensure each item is an associative array with key/value
                    $normalizedOriginalAttr = [];
                    foreach ($originalAttr as $k => $item) {
                        if (is_array($item)) {
                            $normalizedOriginalAttr[] = $item;
                        } elseif (is_string($item)) {
                            // Try to decode string to key/value
                            $decoded = json_decode($item, true);
                            if (is_array($decoded)) {
                                $normalizedOriginalAttr[] = $decoded;
                            } else {
                                $normalizedOriginalAttr[] = ['key' => (string)$k, 'value' => $item];
                            }
                        } else {
                            $normalizedOriginalAttr[] = ['key' => (string)$k, 'value' => (string)$item];
                        }
                    }
                    $originalAttr = $normalizedOriginalAttr;
                    foreach ($this->locales as $locale) {
                        $needTranslateAttr = false;
                        if (empty($variant['attributes'][$locale])) {
                            $needTranslateAttr = true;
                        } else {
                            // If frontend already populated target locale but it's identical to English entries, re-translate
                            $existing = $variant['attributes'][$locale];
                            if (is_array($existing) && json_encode($existing) === json_encode($originalAttr)) {
                                $needTranslateAttr = true;
                            }
                        }

                        if ($needTranslateAttr) {
                            $translatedAttr = [];
                            foreach ($originalAttr as $attrEntry) {
                                $origKey = $attrEntry['key'] ?? null;
                                $val = $attrEntry['value'] ?? '';

                                // Translate both key and value from English -> target locale
                                $translatedKey = null;
                                if (!empty($origKey) && is_string($origKey)) {
                                    $translatedKey = $this->translate($origKey, $locale);
                                }

                                $translatedVal = $this->translate(is_string($val) ? $val : json_encode($val), $locale);
                                $translatedAttr[] = ['key' => $translatedKey ?? $origKey, 'value' => $translatedVal];
                            }
                            $variant['attributes'][$locale] = $translatedAttr;
                            Log::info("Variant {$idx} attributes translated/overwritten to {$locale}");
                        }
                    }
                    // Preserve English after translation
                    $variant['attributes']['en'] = $originalAttr;
                    // Ensure we have values for target locales; fallback to English if translation produced empty
                    foreach ($this->locales as $locale) {
                        if (empty($variant['attributes'][$locale])) {
                            // Deep copy English attributes as fallback
                            $variant['attributes'][$locale] = $originalAttr;
                        }
                    }
                    // Ensure variant_name has fallback for locales as well
                    foreach (['fr','ar'] as $ln) {
                        if (empty($variant['variant_name'][$ln])) {
                            $variant['variant_name'][$ln] = $originalEnglish;
                        }
                    }
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
        // Fast path: empty text -> keep empty
        if (trim($text) === '') {
            return '';
        }

        // Normalize cache key
        $cacheKey = $target . '|' . trim($text);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        try {
            // Prefer auto-detection to be more robust with incoming text
            $this->tr->setSource('auto');
            $this->tr->setTarget($target);

            $result = $this->tr->translate($text);

            // If translator returned identical text, attempt one retry with explicit English source
            if ($result === $text) {
                try {
                    $this->tr->setSource('en');
                    $this->tr->setTarget($target);
                    $result = $this->tr->translate($text);
                } catch (\Exception $inner) {
                    Log::warning("Translation retry failed for target {$target}: " . $inner->getMessage());
                }
            }

            if ($result === $text) {
                Log::warning("Translation returned original text (no change detected) for {$target}");
            }

            // Save to request-scoped cache
            $this->cache[$cacheKey] = $result;

            return $result;
        } catch (\Exception $e) {
            Log::error("Translation FAILED for {$target}: " . $e->getMessage());
            Log::warning("Fallback: Using English text instead");
            $this->cache[$cacheKey] = $text;
            return $text; // Fallback to English on failure
        }
    }
}
