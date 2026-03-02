<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    private const SESSION_KEY = 'compare_variant_ids';
    private const MAX_ITEMS = 4;

    public function index(Request $request)
    {
        $variantIds = $this->getSessionVariantIds();

        $variants = ProductVariant::query()
            ->with(['product.brand', 'product.category', 'reviews'])
            ->whereIn('id', $variantIds)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $orderedVariants = collect($variantIds)
            ->map(fn (int $id) => $variants->get($id))
            ->filter()
            ->values();

        $features = $this->collectFeatureKeys($orderedVariants);

        return view('client.pages.compare', [
            'compareVariants' => $orderedVariants,
            'features' => $features,
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
        ]);

        $variantId = (int) $validated['product_variant_id'];
        $ids = $this->getSessionVariantIds();

        $variant = ProductVariant::query()
            ->whereKey($variantId)
            ->where('status', 'active')
            ->first();

        if (! $variant) {
            return response()->json([
                'success' => false,
                'message' => 'This product is not available for comparison.',
                'count' => count($ids),
                'ids' => $ids,
            ], 422);
        }

        if (in_array($variantId, $ids, true)) {
            return response()->json([
                'success' => true,
                'message' => 'Product already in compare list.',
                'count' => count($ids),
                'ids' => $ids,
            ]);
        }

        if (count($ids) >= self::MAX_ITEMS) {
            return response()->json([
                'success' => false,
                'message' => 'You can compare up to 4 products only.',
                'count' => count($ids),
                'ids' => $ids,
            ], 422);
        }

        $ids[] = $variantId;
        $this->storeSessionVariantIds($ids);

        return response()->json([
            'success' => true,
            'message' => 'Product added to compare list.',
            'count' => count($ids),
            'ids' => $ids,
        ], 201);
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'integer'],
        ]);

        $variantId = (int) $validated['product_variant_id'];
        $ids = array_values(array_filter(
            $this->getSessionVariantIds(),
            fn (int $id) => $id !== $variantId,
        ));

        $this->storeSessionVariantIds($ids);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from compare list.',
                'count' => count($ids),
                'ids' => $ids,
            ]);
        }

        return back()->with('success', 'Product removed from compare list.');
    }

    public function get(Request $request)
    {
        $ids = $this->getSessionVariantIds();

        return response()->json([
            'success' => true,
            'count' => count($ids),
            'ids' => $ids,
        ]);
    }

    public function count(Request $request)
    {
        $ids = $this->getSessionVariantIds();

        return response()->json([
            'success' => true,
            'count' => count($ids),
        ]);
    }

    public function clear(Request $request)
    {
        $request->session()->forget(self::SESSION_KEY);

        return response()->json([
            'success' => true,
            'message' => 'Compare list cleared.',
            'count' => 0,
            'ids' => [],
        ]);
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

        $activeIds = ProductVariant::query()
            ->whereIn('id', $normalized)
            ->where('status', 'active')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

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
        $map = [
            'Brand' => (string) ($variant->product?->brand?->name ?? '—'),
            'Category' => (string) ($variant->product?->category?->name ?? '—'),
            'SKU' => (string) ($variant->sku ?? '—'),
        ];

        $attributes = $variant->getTranslation('attributes', app()->getLocale()) ?? $variant->attributes;
        $specifications = $variant->product?->getTranslation('specifications', app()->getLocale())
            ?? $variant->product?->specifications;

        foreach ([$attributes, $specifications] as $source) {
            if (! is_array($source)) {
                continue;
            }

            foreach ($source as $key => $value) {
                if (! is_string($key) || trim($key) === '') {
                    continue;
                }

                if (is_array($value)) {
                    $value = implode(', ', array_filter(array_map('strval', $value)));
                }

                $map[$key] = (string) ($value ?: '—');
            }
        }

        return $map;
    }
}
