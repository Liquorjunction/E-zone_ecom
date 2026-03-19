<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Repositories\Client\Interfaces\CompareRepositoryInterface as CompareRepo;

class CompareService
{
    private const SESSION_KEY = 'compare_variant_ids';
    private const MAX_ITEMS = 4;

    protected $compareRepo;

    public function __construct(CompareRepo $compareRepo)
    {
        $this->compareRepo = $compareRepo;
    }

    public function buildIndexData(): array
    {
        $variantIds = $this->getSessionVariantIds();

        $variants = $this->compareRepo->getActiveVariantsByIds($variantIds)->keyBy('id');

        $orderedVariants = collect($variantIds)
            ->map(fn (int $id) => $variants->get($id))
            ->filter()
            ->values();

        return [
            'compareVariants' => $orderedVariants,
            'features' => $this->collectFeatureKeys($orderedVariants),
        ];
    }

    public function addVariant(int $variantId): array
    {
        $ids = $this->getSessionVariantIds();
        $variant = $this->compareRepo->getActiveVariantById($variantId);

        if (! $variant) {
            return $this->response(false, statictranslations('This product is not available for comparison.'), $ids, 422);
        }

        if (in_array($variantId, $ids, true)) {
            return $this->response(true, statictranslations('Product already in compare list.'), $ids, 200);
        }

        if (count($ids) >= self::MAX_ITEMS) {
            return $this->response(false, statictranslations('You can compare up to 4 products only.'), $ids, 422);
        }

        $existingVariants = $this->compareRepo->getActiveVariantsByIds($ids);

        if ($existingVariants->isNotEmpty()) {
            $existingCategoryId = (int) ($existingVariants->first()?->product?->category_id ?? 0);
            $incomingCategoryId = (int) ($variant->product?->category_id ?? 0);

            if ($existingCategoryId > 0 && $incomingCategoryId > 0 && $existingCategoryId !== $incomingCategoryId) {
                return $this->response(false, statictranslations('Only products from the same category can be compared.'), $ids, 422);
            }

            $existingProductIds = $existingVariants
                ->pluck('product_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if (in_array((int) $variant->product_id, $existingProductIds, true)) {
                return $this->response(false, statictranslations('A product is already in compare list. Remove it to compare another variant.'), $ids, 422);
            }
        }

        $ids[] = $variantId;
        $this->storeSessionVariantIds($ids);

        return $this->response(true, statictranslations('Product added to compare list.'), $ids, 201);
    }

    public function removeVariant(int $variantId): array
    {
        $ids = array_values(array_filter(
            $this->getSessionVariantIds(),
            fn (int $id) => $id !== $variantId,
        ));

        $this->storeSessionVariantIds($ids);

        return $this->response(true, statictranslations('Product removed from compare list.'), $ids, 200);
    }

    public function getState(): array
    {
        $ids = $this->getSessionVariantIds();

        return [
            'success' => true,
            'count' => count($ids),
            'ids' => $ids,
        ];
    }

    public function getCount(): array
    {
        $ids = $this->getSessionVariantIds();

        return [
            'success' => true,
            'count' => count($ids),
        ];
    }

    public function clear(): array
    {
        session()->forget(self::SESSION_KEY);

        return [
            'success' => true,
            'message' => statictranslations('Compare list cleared.'),
            'count' => 0,
            'ids' => [],
        ];
    }

    private function response(bool $success, string $message, array $ids, int $status): array
    {
        return [
            'success' => $success,
            'message' => $message,
            'count' => count($ids),
            'ids' => array_values($ids),
            'status' => $status,
        ];
    }

    private function getSessionVariantIds(): array
    {
        $ids = session(self::SESSION_KEY, []);

        $normalized = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->take(self::MAX_ITEMS)
            ->values()
            ->all();

        if (empty($normalized)) {
            return [];
        }

        $activeIds = $this->compareRepo->getActiveIds($normalized);

        $ordered = collect($normalized)
            ->filter(fn (int $id) => in_array($id, $activeIds, true))
            ->values()
            ->all();

        if ($ordered !== $normalized) {
            $this->storeSessionVariantIds($ordered);
        }

        return $ordered;
    }

    private function storeSessionVariantIds(array $ids): void
    {
        session([self::SESSION_KEY => collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->take(self::MAX_ITEMS)
            ->values()
            ->all()]);
    }

    private function collectFeatureKeys($variants): array
    {
        $keys = collect();

        foreach ($variants as $variant) {
            $specs = $this->specMap($variant);
            $keys = $keys->merge(array_keys($specs));
        }

        $keys = $keys
            ->filter(fn ($key) => is_string($key) && trim($key) !== '')
            ->unique()
            ->values();

        if ($keys->isEmpty()) {
            return ['Brand', 'Category', 'SKU'];
        }

        return $keys->take(12)->all();
    }

    private function specMap(ProductVariant $variant): array
    {
        $locale = app()->getLocale();

        $map = [
            'Brand' => (string) ($variant->product?->brand?->name ?? '—'),
            'Category' => $this->resolveCategoryName($variant->product?->category, $locale),
            'SKU' => (string) ($variant->sku ?? '—'),
        ];

        $sources = [
            $this->localizedAttribute($variant, 'attributes', $locale),
            $this->localizedAttribute($variant, 'specifications', $locale),
            $this->localizedAttribute($variant->product, 'attributes', $locale),
            $this->localizedAttribute($variant->product, 'specifications', $locale),
        ];

        foreach ($sources as $source) {
            foreach ($this->normalizeSpecSource($source) as $key => $value) {
                $map[$key] = $value;
            }
        }

        return $map;
    }

    private function normalizeSpecSource($source): array
    {
        $locale = app()->getLocale();

        if (is_string($source)) {
            $decoded = json_decode($source, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $source = $decoded;
            } else {
                return [];
            }
        }

        if (! is_array($source)) {
            return [];
        }

        if ($this->isLocaleBucket($source, $locale)) {
            $source = $source[$locale] ?? ($source['en'] ?? reset($source));
            if (! is_array($source)) {
                return [];
            }
        }

        $normalized = [];

        foreach ($source as $key => $value) {
            if (is_array($value) && (isset($value['title']) || isset($value['label']) || isset($value['value']) || isset($value['description']))) {
                $title = $value['title'] ?? ($value['label'] ?? (is_string($key) ? $key : null));
                $displayValue = $value['value'] ?? ($value['description'] ?? null);

                $title = $this->normalizeDisplayValue($title, $locale);
                $displayValue = $this->normalizeDisplayValue($displayValue, $locale);

                if (! empty($title) && ! empty($displayValue)) {
                    $normalized[$title] = $displayValue;
                }

                continue;
            }

            if (! is_string($key) || trim($key) === '') {
                continue;
            }

            $displayValue = $this->normalizeDisplayValue($value, $locale);
            $normalized[$key] = $displayValue !== '' ? $displayValue : '—';
        }

        return $normalized;
    }

    private function isLocaleBucket(array $source, string $locale): bool
    {
        $keys = array_keys($source);
        $knownLocales = ['en', 'fr', 'ar', $locale];

        return ! empty($keys)
            && count(array_filter($keys, fn ($key) => is_string($key) && in_array($key, $knownLocales, true))) >= 1
            && count(array_filter($keys, fn ($key) => is_string($key) && ! in_array($key, $knownLocales, true))) === 0;
    }

    private function normalizeDisplayValue($value, string $locale): string
    {
        if (is_array($value)) {
            if ($this->isLocaleBucket($value, $locale)) {
                $picked = $value[$locale] ?? ($value['en'] ?? reset($value));

                if (is_array($picked)) {
                    return implode(', ', array_filter(array_map('strval', $picked)));
                }

                return trim((string) $picked);
            }

            return implode(', ', array_filter(array_map('strval', $value)));
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_null($value)) {
            return '—';
        }

        return trim((string) $value);
    }

    private function localizedAttribute($model, string $attribute, string $locale)
    {
        if (! $model) {
            return null;
        }

        if (method_exists($model, 'getTranslation') && method_exists($model, 'getTranslatableAttributes')) {
            $translatable = $model->getTranslatableAttributes();

            if (in_array($attribute, $translatable, true)) {
                return $model->getTranslation($attribute, $locale);
            }
        }

        return $model->{$attribute} ?? null;
    }

    private function resolveCategoryName($category, string $locale): string
    {
        if (! $category) {
            return '—';
        }

        if (method_exists($category, 'getTranslation') && method_exists($category, 'getTranslatableAttributes')) {
            $translatable = $category->getTranslatableAttributes();

            if (in_array('category-name', $translatable, true)) {
                $value = $category->getTranslation('category-name', $locale);

                if (is_string($value) && trim($value) !== '') {
                    return $value;
                }
            }
        }

        $raw = $category->getAttribute('category-name') ?? $category->getAttribute('name');

        if (is_array($raw)) {
            $picked = $raw[$locale] ?? ($raw['en'] ?? reset($raw));

            return is_string($picked) && trim($picked) !== '' ? $picked : '—';
        }

        return is_string($raw) && trim($raw) !== '' ? $raw : '—';
    }
}
