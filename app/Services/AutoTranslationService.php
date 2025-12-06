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

        foreach ($fields as $field) {
            if (isset($data[$field]['en']) && !empty($data[$field]['en'])) {
                foreach ($this->locales as $locale) {
                    // Only translate if target is empty
                    if (empty($data[$field][$locale])) {
                        $data[$field][$locale] = $this->translate($data[$field]['en'], $locale);
                    }
                }
            }
        }

        // 2. Complex JSON (Specifications)
        // Structure: [['title' => 'Screen', 'value' => 'OLED']]
        if (isset($data['specifications']['en']) && is_array($data['specifications']['en'])) {
            foreach ($this->locales as $locale) {
                $translatedSpecs = [];
                foreach ($data['specifications']['en'] as $spec) {
                    $translatedSpecs[] = [
                        'title' => $this->translate($spec['title'] ?? '', $locale),
                        'value' => $this->translate($spec['value'] ?? '', $locale),
                    ];
                }
                // Save ONLY if the user didn't manually provide this locale
                if (empty($data['specifications'][$locale])) {
                    $data['specifications'][$locale] = $translatedSpecs;
                }
            }
        }

        return $data;
    }

    private function translate(string $text, string $target): string
    {
        if (empty($text)) return '';
        try {
            $this->tr->setSource('en');
            $this->tr->setTarget($target);
            return $this->tr->translate($text);
        } catch (\Exception $e) {
            Log::error("Translation failed for {$target}: " . $e->getMessage());
            return $text; // Fallback to English on failure
        }
    }
}
