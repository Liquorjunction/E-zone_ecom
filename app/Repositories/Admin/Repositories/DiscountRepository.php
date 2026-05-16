<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Discount;
use App\Repositories\Admin\Interfaces\DiscountRepositoryInterface;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Brand;
use App\Models\DiscountTarget;
use App\Models\Product;
use App\Models\ProductVariant;

class DiscountRepository implements DiscountRepositoryInterface
{
    public function all()
    {
        return Discount::all();
    }

    public function find($id)
    {
        return Discount::find($id);
    }

    public function create(array $data)
    {
        return Discount::create($data);
    }

    public function update($id, array $data)
    {
        $discount = Discount::find($id);
        if ($discount) {
            $discount->update($data);
            return $discount;
        }
        return false;
    }

    public function delete($id)
    {
        $discount = Discount::find($id);
        if ($discount) {
            // Detach/Delete targets first if strictly needed,
            // but cascading database deletes usually handle this.
            $discount->targets()->delete();
            return $discount->delete();
        }
        return false;
    }

    public function getDiscountById($id)
    {
        return Discount::with('targets.target')->find($id);
    }

    public function updateStatus($id)
    {
        $discount = Discount::find($id);
        if ($discount) {
            $discount->status = !$discount->status;
            $discount->save();
            return $discount;
        }
        return false;
    }

    public function getDiscountTypeList()
    {
        return Discount::query()->select('type')->distinct()->pluck('type');
    }

    /**
     * Return Query Builder for DataTables
     */
    public function getDiscountsForDataTable($request)
    {
        $query = Discount::query()->with(['targets']);

        // Status filter
        $status = is_array($request) ? ($request['status'] ?? null) : $request->input('status');
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status === 'active' ? 1 : 0);
        }

        // Date range filter
        $dateFrom = is_array($request) ? ($request['date_from'] ?? null) : $request->input('date_from');
        $dateTo = is_array($request) ? ($request['date_to'] ?? null) : $request->input('date_to');
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        // Type filter
        $type = is_array($request) ? ($request['type'] ?? null) : $request->input('type');
        if (!empty($type)) {
            $query->where('type', $type);
        }

        // Validity filter: active/upcoming/expired based on starts_at and ends_at
        $validity = is_array($request) ? ($request['validity'] ?? null) : $request->input('validity');
        if (in_array($validity, ['active', 'upcoming', 'expired'], true)) {
            $now = now();

            if ($validity === 'active') {
                $query->where(function ($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })->where(function ($q) use ($now) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                });
            }

            if ($validity === 'upcoming') {
                $query->whereNotNull('starts_at')->where('starts_at', '>', $now);
            }

            if ($validity === 'expired') {
                $query->whereNotNull('ends_at')->where('ends_at', '<', $now);
            }
        }

        return $query;
    }

    /**
     * Return mapped product variants for selects (id + label)
     */
    public function getProductVariants()
    {
        return ProductVariant::with('product')->get()->map(function ($v) {
            $productName = $v->product ? (@$v->product->getTranslation('name', 'en') ?? null) : null;
            $variantName = method_exists($v, 'getTranslation') ? @call_user_func([$v, 'getTranslation'], 'variant_name', 'en') : ($v->variant_name ?? null);
            $label = $productName ? ($productName . ' / ' . ($variantName ?: $v->sku)) : ($variantName ?: $v->sku ?: ('Variant ' . $v->id));
            return (object)['id' => $v->id, 'name' => $label];
        })->values();
    }

    /**
     * Format a collection/array of DiscountTarget models into simple arrays
     * with resolved names (handles product variants).
     */
    public function formatTargetsCollection($items)
    {
        $collection = collect($items);
        if ($collection->isEmpty()) {
            return [];
        }

        $result = $collection->map(function ($t) {
            $related = $t->target ?? null;
            $name = null;

            if ($related) {
                if ($related instanceof ProductVariant) {
                    $product = $related->product ?? null;
                    $variantName = method_exists($related, 'getTranslation') ? @call_user_func([$related, 'getTranslation'], 'variant_name', 'en') : ($related->variant_name ?? null);
                    $productName = null;
                    if ($product && method_exists($product, 'getTranslation')) {
                        try { $productName = $product->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                    }
                    if (!empty($productName)) {
                        $name = $productName . ' / ' . ($variantName ?: ($related->sku ?? $related->id));
                    } else {
                        $name = $variantName ?: ($related->sku ?? ('Variant ' . $related->id));
                    }
                } elseif ($related instanceof Product) {
                    // If a Product and ProductVariant have the same ID, and a variant exists, always show both
                    $variant = ProductVariant::find($t->target_id);
                    if ($variant) {
                        $product = $variant->product ?? null;
                        $productName = null;
                        $variantName = method_exists($variant, 'getTranslation') ? @call_user_func([$variant, 'getTranslation'], 'variant_name', 'en') : ($variant->variant_name ?? null);
                        if ($product && method_exists($product, 'getTranslation')) {
                            try { $productName = $product->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                        }
                        if (!empty($productName)) {
                            $name = $productName . ' / ' . ($variantName ?: ($variant->sku ?? $variant->id));
                        } else {
                            $name = $variantName ?: ($variant->sku ?? ('Variant ' . $variant->id));
                        }
                    } else {
                        if (method_exists($related, 'getTranslation')) {
                            try { $name = $related->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                        }
                        $name = $name ?: ($related->name ?? null);
                    }
                } else {
                    if (method_exists($related, 'getTranslation')) {
                        try { $name = $related->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                    }
                    $name = $name ?: ($related->name ?? ($related->title ?? null));
                }
            }

            if (empty($name) && !empty($t->target_type)) {
                $type = strtolower($t->target_type);
                try {
                    switch ($type) {
                        case 'category':
                            $cat = Categories::find($t->target_id);
                            if ($cat && method_exists($cat, 'getTranslation')) {
                                try { $name = $cat->getTranslation('category-name', 'en'); } catch (\Throwable $e) { }
                            }
                            break;
                        case 'subcategory':
                            $sc = SubCategories::find($t->target_id);
                            if ($sc && method_exists($sc, 'getTranslation')) {
                                try { $name = $sc->getTranslation('sub-category-name', 'en'); } catch (\Throwable $e) { }
                            }
                            break;
                        case 'product':
                            // If a Product and ProductVariant have the same ID, and a variant exists, always show both
                            $variant = ProductVariant::find($t->target_id);
                            if ($variant) {
                                $product = $variant->product ?? null;
                                $productName = null;
                                $variantName = method_exists($variant, 'getTranslation') ? @call_user_func([$variant, 'getTranslation'], 'variant_name', 'en') : ($variant->variant_name ?? null);
                                if ($product && method_exists($product, 'getTranslation')) {
                                    try { $productName = $product->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                                }
                                if (!empty($productName)) {
                                    $name = $productName . ' / ' . ($variantName ?: ($variant->sku ?? $variant->id));
                                } else {
                                    $name = $variantName ?: ($variant->sku ?? ('Variant ' . $variant->id));
                                }
                            } else {
                                $prod = Product::find($t->target_id);
                                if ($prod && method_exists($prod, 'getTranslation')) {
                                    try { $name = $prod->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                                }
                                $name = $name ?: ($prod->name ?? null);
                            }
                            break;
                        default:
                            break;
                    }
                } catch (\Throwable $e) {
                    // swallow
                }
            }

            if (!empty($name) && is_string($name)) {
                $name = html_entity_decode($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }

            return [
                'target_type' => $t->target_type,
                'target_id' => $t->target_id ?? null,
                'name' => $name,
                'status' => isset($t->status) ? (bool) $t->status : null,
            ];
        })->values()->all();

        return $result;
    }

    /**
     * Return select lists for categories, subcategories, brands and products (id + name)
     */
    public function getSelectLists()
    {
        $categories = Categories::all()->map(function ($c) {
            return (object)['id' => $c->id, 'name' => ($c->getTranslation('category-name', 'en') ?? $c->name ?? null)];
        })->values();

        $subcategories = SubCategories::all()->map(function ($s) {
            return (object)['id' => $s->id, 'name' => ($s->getTranslation('sub-category-name', 'en') ?? $s->name ?? null)];
        })->values();

        $brands = Brand::all()->map(function ($b) {
            return (object)['id' => $b->id, 'name' => ($b->getTranslation('name', 'en') ?? $b->name ?? null)];
        })->values();

        $products = Product::all()->map(function ($p) {
            return (object)['id' => $p->id, 'name' => ($p->getTranslation('name', 'en') ?? $p->name ?? null)];
        })->values();

        return compact('categories', 'subcategories', 'brands', 'products');
    }

    public function hasDuplicateActiveTargets(array $targets): bool
    {
        foreach ($targets as $target) {
            if (!empty($target['target_type']) && !empty($target['target_id'])) {
                $exists = DiscountTarget::where('target_type', $target['target_type'])
                    ->where('target_id', $target['target_id'])
                    ->whereHas('discount', function ($q) {
                        $q->where('status', true)
                          ->where(function ($query) {
                              $now = now();
                              $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                              $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                          });
                    })
                    ->exists();
                if ($exists) {
                    return true;
                }
            }
        }
        return false;
    }
}
