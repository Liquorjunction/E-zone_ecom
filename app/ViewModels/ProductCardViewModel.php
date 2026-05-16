<?php

namespace App\ViewModels;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductCardViewModel
{
    public function __construct(
        private readonly Product|ProductVariant $product,
        private readonly array $context = [],
    ) {
    }

    public function toArray(): array
    {
        $locale = app()->getLocale() ?: 'en';
        $isVariant = $this->product instanceof ProductVariant;
        $master = $isVariant ? $this->product->product : $this->product;

        if (! $master) {
            return [];
        }

        $productDiscountInfo = $master->getDiscountInfo();
        $itemDiscountInfo = $isVariant ? $this->product->getDiscountInfo() : $productDiscountInfo;
        $variantId = $this->resolveVariantId($master, $isVariant);
        $reviews = $this->resolveReviews($master, $isVariant);
        $approvedReviews = $reviews->where('is_approved', 'approved');
        $reviewCount = $approvedReviews->count();

        return [
            'locale' => $locale,
            'isVariant' => $isVariant,
            'master' => $master,
            'name' => $this->resolveName($master, $locale, $isVariant),
            'categoryName' => $this->resolveCategoryName($master),
            'imagePath' => $this->resolveImagePath($master),
            'real' => $this->resolveRealPrice($master, $isVariant),
            'itemDiscountInfo' => $itemDiscountInfo,
            'productDiscountInfo' => $productDiscountInfo,
            'variantId' => $variantId,
            'isWishlisted' => $this->resolveWishlistState($variantId),
            'isCompared' => $this->resolveCompareState($variantId),
            'isBestseller' => $this->resolveBestseller($master, $isVariant),
            'stock' => $this->resolveStock($master, $isVariant),
            'reviewCount' => $reviewCount,
            'averageRating' => $reviewCount > 0 ? round($approvedReviews->avg('rating'), 1) : 0,
            'warranty' => $this->resolveWarranty($master, $locale),
            'productUrl' => $this->resolveProductUrl($master),
            'isInCart' => $this->resolveCartState($master, $isVariant),
        ];
    }

    private function resolveName(Product $master, string $locale, bool $isVariant): string
    {
        $rawName = $isVariant
            ? $this->product->getTranslation('variant_name', $locale) ?? ''
            : $master->getTranslation('name', $locale) ?? '';

        return $this->normalizeValue($rawName, $isVariant ? 'Variant' : 'Product');
    }

    private function resolveCategoryName(Product $master): string
    {
        $rawCategory = $master->category?->{'category-name'} ?? ($master->category?->name ?? 'Uncategorized');

        return $this->normalizeValue($rawCategory, 'Uncategorized');
    }

    private function resolveImagePath(Product $master): string
    {
        return $master->cover_image
            ? asset('storage/' . $master->cover_image)
            : asset('icon/product-placeholder-A.jpg');
    }

    private function resolveRealPrice(Product $master, bool $isVariant): float|int|string
    {
        if ($isVariant) {
            return $this->product->price ?? ($master->real_price ?? ($master->price ?? 0));
        }

        return $master->real_price ?? ($master->price ?? 0);
    }

    private function resolveVariantId(Product $master, bool $isVariant): ?int
    {
        if ($isVariant) {
            return $this->product->id ?? null;
        }

        $firstVariant = $master->productVariants && $master->productVariants->count() > 0
            ? $master->productVariants->first()
            : null;

        return $firstVariant?->id ?? $master->id;
    }

    private function resolveWishlistState(?int $variantId): bool
    {
        $wishlistVariantIds = (array) ($this->context['wishlistVariantIds'] ?? []);

        return $variantId ? in_array($variantId, $wishlistVariantIds) : false;
    }

    private function resolveCompareState(?int $variantId): bool
    {
        $compareVariantIds = collect(session('compare_variant_ids', []))->map(fn ($id) => (int) $id)->all();

        return $variantId ? in_array((int) $variantId, $compareVariantIds, true) : false;
    }

    private function resolveBestseller(Product $master, bool $isVariant): bool
    {
        if ($isVariant) {
            return (bool) $this->product->is_bestseller;
        }

        $firstVariant = $master->productVariants?->first();

        return $firstVariant && (bool) $firstVariant->is_bestseller;
    }

    private function resolveStock(Product $master, bool $isVariant): int
    {
        if ($isVariant) {
            return method_exists($this->product, 'getSellableStock') ? (int) $this->product->getSellableStock() : 0;
        }

        if ($master->productVariants && $master->productVariants->count() > 0) {
            return (int) $master->productVariants->sum(function ($variant) {
                return method_exists($variant, 'getSellableStock') ? $variant->getSellableStock() : 0;
            });
        }

        return 0;
    }

    private function resolveReviews(Product $master, bool $isVariant): Collection
    {
        if ($isVariant) {
            return $this->product->reviews ?? collect();
        }

        $reviews = collect();

        if ($master->productVariants && $master->productVariants->count() > 0) {
            foreach ($master->productVariants as $variant) {
                if ($variant->reviews) {
                    $reviews = $reviews->merge($variant->reviews);
                }
            }
        } elseif ($master->reviews) {
            $reviews = $master->reviews;
        }

        return $reviews;
    }

    private function resolveWarranty(Product $master, string $locale): string
    {
        $warrantyRaw = $master->getTranslation('warranty', $locale) ?? '';
        $warranty = $this->normalizeValue($warrantyRaw, '1 Year Warranty');

        return $warranty ?: '1 Year Warranty';
    }

    private function resolveProductUrl(Product $master): string
    {
        $brandSlug = $this->context['brand_slug'] ?? null;
        $categorySlug = $this->context['category_slug'] ?? null;
        $subcategorySlug = $this->context['subcategory_slug'] ?? null;
        $productUrl = null;

        if (! empty($brandSlug)) {
            $productUrl = route('products.detail.brand', [
                'brandSlug' => $brandSlug,
                'slug' => $master->slug,
            ]);
        } elseif (! empty($categorySlug) && ! empty($subcategorySlug)) {
            $productUrl = route('products.detail.category-subcategory', [
                'categorySlug' => $categorySlug,
                'subcategorySlug' => $subcategorySlug,
                'slug' => $master->slug,
            ]);
        } elseif (! empty($categorySlug)) {
            $productUrl = route('products.detail.category', [
                'categorySlug' => $categorySlug,
                'slug' => $master->slug,
            ]);
        } elseif (! empty($subcategorySlug)) {
            $resolvedCategorySlug = $categorySlug ?? optional($master->category)->slug;

            if (! empty($resolvedCategorySlug)) {
                $productUrl = route('products.detail.category-subcategory', [
                    'categorySlug' => $resolvedCategorySlug,
                    'subcategorySlug' => $subcategorySlug,
                    'slug' => $master->slug,
                ]);
            }
        }

        if (! $productUrl) {
            $productUrl = route('products.detail', $master->slug);
        }

        if ($this->product instanceof ProductVariant) {
            $productUrl .= '?variant=' . ($this->product->slug ?? '');
        }

        return $productUrl;
    }

    private function resolveCartState(Product $master, bool $isVariant): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        $sessionId = session()->getId();
        $cartQuery = $isVariant ? $this->product->cartItems() : $master->cartItems();

        return $cartQuery->whereHas('cart', function ($query) use ($user, $sessionId) {
            $query->where('user_id', $user->id)
                ->where(function ($inner) use ($sessionId) {
                    $inner->whereNull('session_id')
                        ->orWhere('session_id', $sessionId);
                })
                ->where(function ($inner) {
                    $inner->whereNull('session_id')
                        ->orWhere('session_id', 'not like', '%:buy-now');
                });
        })->exists();
    }

    private function normalizeValue(mixed $value, string $fallback): string
    {
        if (is_array($value)) {
            $value = implode(' ', array_filter($value));
        }

        $value = trim((string) $value);

        return $value !== '' ? $value : $fallback;
    }
}
