<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements ProductRepositoryInterface
{
    // ─── Shared base constraints for "active product + active associations" ───
    // FIX: extracted into a private helper to avoid repeating the same 6-line
    //      whereHas block in every method. DRY principle.
    private function applyActiveProductConstraints(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereHas('brand',     fn (Builder $q) => $q->where('status', 'active'))
            ->whereHas('category',  fn (Builder $q) => $q->where('status', 'active'))
            ->where(fn (Builder $q) =>
                $q->whereHas('subCategory', fn (Builder $scq) => $scq->where('status', 'active'))
                  ->orWhereNull('sub_category_id')
            );
    }

    // ─── Shared eager-load set used by variant listings ───
    private function variantWithRelations(): array
    {
        return [
            'images'  => fn ($q) => $q->orderBy('sort_order'),
            'inventory',
            'reviews' => fn ($q) => $q->where('is_approved', 'approved'),
            'product' => fn ($q) => $q->where('status', 'active')
                                      ->with(['category', 'brand', 'images']),
        ];
    }

    // ─── Apply common variant filters (search, category, brand, subcategory,
    //     price, rating, availability, discount, sort) to a Builder. ───
    // FIX: category+subcategory were applied as separate AND constraints,
    //      meaning "category A AND subcategory of category B" could return zero
    //      results. They are now applied together correctly (subcategory already
    //      implies its parent category, so we filter on subcategory only when
    //      provided, and on category when no subcategory is given).
    private function applyVariantFilters(Builder $query, Request $request): Builder
    {
        // Search
        if ($request->filled('search')) {
            $like = '%' . $request->get('search') . '%';
            $query->where(fn (Builder $q) =>
                $q->where('variant_name', 'LIKE', $like)
                  ->orWhere('sku', 'LIKE', $like)
                  ->orWhereHas('product', fn (Builder $pq) =>
                      $pq->where('name', 'LIKE', $like)
                         ->orWhere('description', 'LIKE', $like)
                  )
            );
        }

        // Brand
        if ($request->filled('brand')) {
            $brands = (array) $request->brand;
            $query->whereHas('product.brand', fn (Builder $q) =>
                $q->whereIn('slug', $brands)->where('status', 'active')
            );
        }

        // FIX: category + subcategory are now OR-based per filter group.
        //      If subcategories are selected, we filter by subcategory slug (which
        //      already scopes to the right category). If only categories are
        //      selected we filter by category slug. This prevents the AND trap.
        $categories    = $request->filled('category')    ? (array) $request->category    : [];
        $subcategories = $request->filled('subcategory') ? (array) $request->subcategory : [];

        if (!empty($subcategories)) {
            $query->whereHas('product.subCategory', fn (Builder $q) =>
                $q->whereIn('slug', $subcategories)->where('status', 'active')
            );
        } elseif (!empty($categories)) {
            $query->whereHas('product.category', fn (Builder $q) =>
                $q->whereIn('slug', $categories)->where('status', 'active')
            );
        }

        // Bestseller
        if ($request->filled('bestseller') && $request->bestseller) {
            $query->where('is_bestseller', true);
        }

        // Price
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Rating — FIX: use subquery-style havingRaw; the original had a
        //               groupBy inside whereHas which can produce incorrect
        //               counts depending on the DB engine.
        if ($request->filled('rating')) {
            $minRating = min((array) $request->rating);
            $query->whereHas('reviews', fn (Builder $q) =>
                $q->select('product_variant_id')
                  ->where('is_approved', 'approved')
                  ->whereNull('deleted_at')
                  ->groupBy('product_variant_id')
                  ->havingRaw('AVG(rating) >= ?', [$minRating])
            );
        }

        // Availability
        if ($request->filled('availability')) {
            $availability = (array) $request->availability;
            $wantIn       = in_array('in_stock',     $availability);
            $wantOut      = in_array('out_of_stock', $availability);

            // FIX: when BOTH are selected the filter is a no-op (show everything)
            if ($wantIn && !$wantOut) {
                $query->whereHas('inventory', fn (Builder $q) =>
                    $q->whereRaw('(COALESCE(quantity, 0) - COALESCE(reserved_quantity, 0)) > 0')
                );
            } elseif ($wantOut && !$wantIn) {
                $query->whereDoesntHave('inventory', fn (Builder $q) =>
                    $q->whereRaw('(COALESCE(quantity, 0) - COALESCE(reserved_quantity, 0)) > 0')
                );
            }
            // both selected → no constraint (show all)
        }

        // Discount
        if ($request->filled('discount')) {
            $minDiscount = min(array_map('intval', (array) $request->discount));

            // FIX: wrap in a where() group so the orWhereHas doesn't bleed
            //      into other query constraints (was breaking pagination counts)
            $query->where(fn (Builder $outer) =>
                $outer->whereHas('discountTargets', fn (Builder $q) =>
                    $q->whereHas('discount', fn (Builder $dq) =>
                        $dq->where('status', true)
                           ->where('value', '>=', $minDiscount)
                           ->where('starts_at', '<=', now())
                           ->where('ends_at',   '>=', now())
                    )
                )->orWhereHas('product.discountTargets', fn (Builder $q) =>
                    $q->whereHas('discount', fn (Builder $dq) =>
                        $dq->where('status', true)
                           ->where('value', '>=', $minDiscount)
                           ->where('starts_at', '<=', now())
                           ->where('ends_at',   '>=', now())
                    )
                )
            );
        }

        // Sort
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'bestseller':
                $query->where('is_bestseller', true)->latest();
                break;
            case 'featured':
            case 'newest':
            default:
                $query->latest();
        }

        return $query;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC METHODS
    // ─────────────────────────────────────────────────────────────────────────

    public function getAllActiveProducts(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->applyActiveProductConstraints(Product::query())
            ->with([
                'category',
                'brand',
                'images'          => fn ($q) => $q->orderBy('sort_order'),
                'reviews'         => fn ($q) => $q->where('is_approved', 'approved'),
                'productVariants' => fn ($q) => $q->where('status', 'active')
                                                    ->with(['reviews' => fn ($rq) => $rq->where('is_approved', 'approved')]),
            ]);

        // FIX: same category/subcategory OR-logic for the product-level query
        $categories    = $request->filled('category')    ? (array) $request->category    : [];
        $subcategories = $request->filled('subcategory') ? (array) $request->subcategory : [];

        if (!empty($subcategories)) {
            $query->whereHas('subCategory', fn (Builder $q) =>
                $q->whereIn('slug', $subcategories)->where('status', 'active')
            );
        } elseif (!empty($categories)) {
            $query->whereHas('category', fn (Builder $q) =>
                $q->whereIn('slug', $categories)->where('status', 'active')
            );
        }

        if ($request->filled('brand')) {
            $brands = (array) $request->brand;
            $query->whereHas('brand', fn (Builder $q) =>
                $q->whereIn('slug', $brands)->where('status', 'active')
            );
        }

        if ($request->filled('bestseller') && $request->bestseller) {
            $query->where('is_bestseller', true);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        switch ($request->get('sort')) {
            case 'price_asc':  $query->orderBy('price', 'asc');  break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'bestseller': $query->where('is_bestseller', true)->latest(); break;
            default:           $query->latest();
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getAllActiveVariants(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = ProductVariant::query()
            ->where('status', 'active')
            ->with($this->variantWithRelations())
            ->whereHas('product', fn (Builder $q) =>
                $this->applyActiveProductConstraints($q)
            );

        $this->applyVariantFilters($query, $request);

        $paginator = $query->paginate($perPage)->withQueryString();

        // Ensure product relation is loaded (safety net for lazy loads)
        $paginator->getCollection()->each(function (ProductVariant $variant) {
            if (!$variant->relationLoaded('product') || !$variant->product) {
                $variant->load('product.category', 'product.brand', 'product.images');
            }
        });

        return $paginator;
    }

    public function getDealVariants(Request $request, int $perPage = 12): LengthAwarePaginator
    {
        $query = ProductVariant::query()
            ->where('status', 'active')
            ->with($this->variantWithRelations())
            ->whereHas('product', fn (Builder $q) =>
                $this->applyActiveProductConstraints($q)
            );

        $this->applyVariantFilters($query, $request);

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->each(function (ProductVariant $variant) {
            if (!$variant->relationLoaded('product') || !$variant->product) {
                $variant->load('product.category', 'product.brand', 'product.images');
            }
        });

        return $paginator;
    }

    public function getProductWithVariants($id)
    {
        return Product::with(['productVariants' => fn ($q) => $q->withTrashed()])
            ->withTrashed()
            ->find($id);
    }

    public function findActiveBySlug(string $slug): ?Product
    {
        return $this->applyActiveProductConstraints(Product::where('slug', $slug))
            ->with([
                'category',
                'brand',
                'images',
                'productVariants' => fn ($q) =>
                    $q->where('status', 'active')
                      ->with([
                          'images'    => fn ($iq) => $iq->orderBy('sort_order'),
                          'inventory',
                          'reviews'   => fn ($rq) => $rq->where('is_approved', 'approved'),
                      ]),
            ])
            ->firstOrFail();
    }

    public function getRelatedProducts(Product $product, int $limit = 4): Collection
    {
        return $this->applyActiveProductConstraints(
            Product::where('category_id', $product->category_id)
                   ->where('id', '!=', $product->id)
        )
            ->with([
                'images'  => fn ($q) => $q->orderBy('sort_order'),
                'reviews' => fn ($q) => $q->where('is_approved', 'approved'),
            ])
            ->take($limit)
            ->get();
    }

    public function searchProducts(string $keyword, int $limit = 10): Collection
    {
        $like = "%{$keyword}%";
        return $this->applyActiveProductConstraints(Product::query())
            ->where(fn (Builder $q) =>
                $q->where('name', 'LIKE', $like)->orWhere('description', 'LIKE', $like)
            )
            ->with([
                'images'  => fn ($q) => $q->orderBy('sort_order'),
                'reviews' => fn ($q) => $q->where('is_approved', 'approved'),
            ])
            ->take($limit)
            ->get();
    }

    public function getDataforproductlisting(int $productId): Collection
    {
        $product = $this->applyActiveProductConstraints(Product::where('id', $productId))
            ->with(['category', 'brand', 'images'])
            ->firstOrFail();

        $productVariants = ProductVariant::where('product_id', $productId)
            ->where('status', 'active')
            ->with([
                'images'    => fn ($q) => $q->orderBy('sort_order'),
                'inventory',
                'reviews'   => fn ($q) => $q->where('is_approved', 'approved'),
            ])
            ->get();

        $productVariants->each(fn (ProductVariant $v) => $v->product = $product);

        return $productVariants;
    }

    public function getBestsellerProducts(int $limit = 12): Collection
    {
        return $this->applyActiveProductConstraints(
            Product::where('is_bestseller', true)
        )
            ->with([
                'category',
                'brand',
                'images'          => fn ($q) => $q->orderBy('sort_order'),
                'reviews'         => fn ($q) => $q->where('is_approved', 'approved'),
                'productVariants' => fn ($q) => $q->where('status', 'active')
                                                    ->with(['reviews' => fn ($rq) => $rq->where('is_approved', 'approved')]),
            ])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getBestsellerVariants(int $limit = 12): Collection
    {
        return ProductVariant::where('status', 'active')
            ->where('is_bestseller', true)
            ->with($this->variantWithRelations())
            ->whereHas('product', fn (Builder $q) =>
                $this->applyActiveProductConstraints($q)
            )
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getAvailabilityCounts(): array
    {
        $base = fn () => ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', fn (Builder $q) =>
                $this->applyActiveProductConstraints($q)
            );

        $inStock = (clone $base())
            ->whereHas('inventory', fn (Builder $q) =>
                $q->whereRaw('(COALESCE(quantity, 0) - COALESCE(reserved_quantity, 0)) > 0')
            )
            ->count();

        $outOfStock = (clone $base())
            ->whereDoesntHave('inventory', fn (Builder $q) =>
                $q->whereRaw('(COALESCE(quantity, 0) - COALESCE(reserved_quantity, 0)) > 0')
            )
            ->count();

        return [
            'in_stock'     => $inStock,
            'out_of_stock' => $outOfStock,
        ];
    }

    public function getMinPrice(): float
    {
        $min = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', fn (Builder $q) =>
                $this->applyActiveProductConstraints($q)
            )
            ->min('price');

        return $min ? round((float) $min, 2) : 0.00;
    }

    public function getMaxPrice(): float
    {
        $max = ProductVariant::query()
            ->where('status', 'active')
            ->whereHas('product', fn (Builder $q) =>
                $this->applyActiveProductConstraints($q)
            )
            ->max('price');

        return $max ? round((float) $max, 2) : 5000.00;
    }
}
