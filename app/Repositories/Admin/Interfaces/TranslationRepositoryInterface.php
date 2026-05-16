<?php

namespace App\Repositories\Admin\Interfaces;

interface TranslationRepositoryInterface
{
    /**
     * Translate text into multiple languages.
     *
     * @param string $text
     * @param array $locales
     * @return array
     */
    public function translateAll(string $text, array $locales): array;
    public function translateBatch(array $fields, array $locales): array;
}
