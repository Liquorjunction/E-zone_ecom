<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\Log;

class AutoTranslationService
{
    protected $locales = ['fr', 'ar'];
    protected $tr;
    protected $enabled = true;
    protected $cache = [];

    public function __construct()
    {
        $this->tr = new GoogleTranslate();
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function prepareProductData(array $data): array
    {
        if (!$this->enabled) {
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

            if (isset($data['specifications']['en']) && is_array($data['specifications']['en'])) {
                foreach ($this->locales as $locale) {
                    if (empty($data['specifications'][$locale])) {
                        $data['specifications'][$locale] = $data['specifications']['en'];
                    }
                }
            }

            return $data;
        }

        $fields = ['name', 'description', 'warranty', 'dimensions'];

        foreach ($fields as $field) {
            if (isset($data[$field]['en']) && !empty($data[$field]['en'])) {
                $englishValue = $data[$field]['en'];
                $originalEnglishValue = $data[$field]['en'];

                foreach ($this->locales as $locale) {
                    if (empty($data[$field][$locale])) {
                        $translatedValue = $this->translate($englishValue, $locale);
                        $data[$field][$locale] = $translatedValue;
                    }
                }

                $data[$field]['en'] = $originalEnglishValue;
            }
        }

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

                if (empty($data['specifications'][$locale])) {
                    $data['specifications'][$locale] = $translatedSpecs;
                }
            }
        } else {
            Log::info("No English specifications found");
        }

        Log::info("Processing variants");
        if (!empty($data['variants']) && is_array($data['variants'])) {
            foreach ($data['variants'] as $idx => $variant) {
                if (!isset($variant['variant_name'])) {
                    if (isset($variant['name'])) {
                        $variant['variant_name'] = $variant['name'];
                    }
                }

                if (isset($variant['variant_name']) && !is_array($variant['variant_name'])) {
                    $variant['variant_name'] = ['en' => $variant['variant_name']];
                }

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

                $variant['variant_name']['en'] = $originalEnglish;

                foreach (['en','fr','ar'] as $ln) {
                    if (isset($variant['variant_name'][$ln]) && is_array($variant['variant_name'][$ln])) {
                        $v = $variant['variant_name'][$ln];
                        if (isset($v[0]) && is_string($v[0]) && count($v) === 1) {
                            $variant['variant_name'][$ln] = $v[0];
                        } elseif (isset($v[0]) && is_string($v[0])) {
                            $variant['variant_name'][$ln] = implode(' / ', array_map('strval', $v));
                        } else {
                            $variant['variant_name'][$ln] = implode(' - ', array_map('strval', array_values($v)));
                        }
                    }
                }

                if (isset($variant['attributes']) && is_array($variant['attributes'])) {
                    $isTranslatable = false;
                    foreach ($variant['attributes'] as $key => $val) {
                        if (in_array($key, ['en', 'fr', 'ar'])) {
                            $isTranslatable = true;
                            break;
                        }
                    }
                    if (! $isTranslatable) {
                        $variant['attributes'] = ['en' => $variant['attributes']];
                    }
                    $originalAttr = $variant['attributes']['en'] ?? [];
                    if (is_string($originalAttr)) {
                        $decoded = json_decode($originalAttr, true);
                        if (is_array($decoded)) {
                            $originalAttr = $decoded;
                        } else {
                            $originalAttr = ['value' => $originalAttr];
                        }
                    }
                    $normalizedOriginalAttr = [];
                    foreach ($originalAttr as $k => $item) {
                        if (is_array($item)) {
                            $normalizedOriginalAttr[] = $item;
                        } elseif (is_string($item)) {
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
                    $variant['attributes']['en'] = $originalAttr;
                    foreach ($this->locales as $locale) {
                        if (empty($variant['attributes'][$locale])) {
                            $variant['attributes'][$locale] = $originalAttr;
                        }
                    }
                    foreach (['fr','ar'] as $ln) {
                        if (empty($variant['variant_name'][$ln])) {
                            $variant['variant_name'][$ln] = $originalEnglish;
                        }
                    }
                }

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
        if (trim($text) === '') {
            return '';
        }

        $cacheKey = $target . '|' . trim($text);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        try {
            $this->tr->setSource('auto');
            $this->tr->setTarget($target);

            $result = $this->tr->translate($text);

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

            $this->cache[$cacheKey] = $result;

            return $result;
        } catch (\Exception $e) {
            Log::error("Translation FAILED for {$target}: " . $e->getMessage());
            Log::warning("Fallback: Using English text instead");
            $this->cache[$cacheKey] = $text;
            return $text;
        }
    }
}
