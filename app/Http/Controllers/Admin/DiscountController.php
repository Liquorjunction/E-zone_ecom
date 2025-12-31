<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Discounts\DestroyDiscountRequest;
use App\Http\Requests\Admin\Discounts\EditDiscountRequest;
use App\Http\Requests\Admin\Discounts\ReadDiscountRequest;
use App\Http\Requests\Admin\Discounts\StoreDiscountRequest;
use App\Http\Requests\Admin\Discounts\UpdateDiscountRequest; // FIXED
use App\Http\Requests\Admin\Discounts\UpdateDiscountTargetRequest;
use App\Repositories\Admin\Interfaces\DiscountRepositoryInterface as DiscountRepo;
use App\Repositories\Admin\Interfaces\DiscountTargetRepositoryInterface as DiscountTargetRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductVariant;

class DiscountController extends Controller
{
    public function index()
    {
        return view('admin.pages.discounts.index');
    }

    /**
     * Format a collection (or array) of DiscountTarget models into a std array
     * containing target_type, target_id, name and status. Handles product variants.
     */
    private function formatTargetsCollection($items)
    {
        $collection = collect($items);

        if ($collection->isEmpty()) {
            return [];
        }

        $result = $collection->map(function ($t) {
            $related = $t->target ?? null;
            $name = null;

            if ($related) {
                switch (strtolower($t->target_type)) {
                    case 'category':
                        if (method_exists($related, 'getTranslation')) {
                            try { $name = $related->getTranslation('category-name', 'en'); } catch (\Throwable $e) { }
                        }
                        break;
                    case 'subcategory':
                        if (method_exists($related, 'getTranslation')) {
                            try { $name = $related->getTranslation('sub-category-name', 'en'); } catch (\Throwable $e) { }
                        }
                        break;
                    case 'product':
                        try {
                            $product = \App\Models\Product::find($t->target_id);
                            if (!$product) {
                                $variant = ProductVariant::find($t->target_id);
                                if ($variant && $variant->product) {
                                    $product = $variant->product;
                                }
                            }
                            if ($product && method_exists($product, 'getTranslation')) {
                                try { $name = $product->getTranslation('name', 'en'); } catch (\Throwable $ex) { }
                            }
                        } catch (\Throwable $e) { }
                        break;
                    default:
                        if (method_exists($related, 'getTranslation')) {
                            try { $name = $related->getTranslation('name', 'en'); } catch (\Throwable $e) { }
                        }
                        break;
                }

                if (empty($name) && isset($related->name)) {
                    $name = $related->name;
                }
                if (empty($name) && isset($related->title)) {
                    $name = $related->title;
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
     * Show the unified create form.
     */
    public function create()
    {
        // Load small reference lists for selects (id + name)
        $categories = Categories::all()->map(function ($c) {
            return (object)['id' => $c->id, 'name' => $c->getTranslation('category-name', 'en')];
        })->values();

        $subcategories = SubCategories::all()->map(function ($s) {
            return (object)['id' => $s->id, 'name' => $s->getTranslation('sub-category-name', 'en')];
        })->values();

        $brands = Brand::all()->map(function ($b) {
            return (object)['id' => $b->id, 'name' => $b->getTranslation('name', 'en')];
        })->values();

        $products = Product::all()->map(function ($p) {
            return (object)['id' => $p->id, 'name' => $p->getTranslation('name', 'en')];
        })->values();

        // Product variants (sellable items) - show as Variant Label (Product / Variant)
        $productVariants = ProductVariant::with('product')->get()->map(function ($v) {
            $productName = $v->product ? $v->product->getTranslation('name', 'en') : null;
            $variantName = method_exists($v, 'getTranslation') ? $v->getTranslation('variant_name', 'en') : ($v->variant_name ?? null);
            $label = $productName ? ($productName . ' / ' . ($variantName ?: $v->sku)) : ($variantName ?: $v->sku ?: ('Variant ' . $v->id));
            return (object)['id' => $v->id, 'name' => $label];
        })->values();

        $resolvedTargets = [];
        return view('admin.pages.discounts.create', compact('categories', 'subcategories', 'brands', 'products', 'productVariants', 'resolvedTargets'));
    }

    /**
     * Store discount and targets in one transaction.
     */
    public function store(StoreDiscountRequest $request, DiscountRepo $discountRepo, DiscountTargetRepo $discountTargetRepo)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            // --- Smart Fill for 'name' translation ---
            if (isset($data['name']['en'])) {
                if (empty($data['name']['fr'])) {
                    $data['name']['fr'] = gtrans($data['name']['en'], 'fr');
                }
                if (empty($data['name']['ar'])) {
                    $data['name']['ar'] = gtrans($data['name']['en'], 'ar');
                }
            }
            // ----------------------------------------

            $discount = $discountRepo->create($data);

            // Process & Add Targets (if any)
            if ($request->has('targets') && !empty($request->targets)) {
                $targetsToInsert = [];
                foreach ($request->targets as $target) {
                    if (!empty($target['target_type']) && !empty($target['target_id'])) {
                        $targetsToInsert[] = [
                            'target_type' => $target['target_type'],
                            'target_id'   => $target['target_id'],
                        ];
                    }
                }
                if (!empty($targetsToInsert)) {
                    $discountTargetRepo->addTargets($discount->id, $targetsToInsert);
                }
            }

            DB::commit();
            return redirect()->route('admin.discounts.index')->with('success', 'Discount created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function storeTarget(Request $request, DiscountTargetRepo $discountTargetRepo)
    {
        try {
            $discountId = $request->input('discount_id');
            $targets = $request->input('targets', []);

            $toAssign = [];
            foreach ($targets as $target) {
                if (!empty($target['target_type']) && !empty($target['target_id'])) {
                    $toAssign[] = [
                        'target_type' => $target['target_type'],
                        'target_id'   => $target['target_id'],
                        'status'      => $target['status'] ?? true,
                    ];
                }
            }

            if ($discountId && count($toAssign)) {
                $discountTargetRepo->addTargets($discountId, $toAssign);
                return redirect()->route('admin.discounts.index')->with('success', 'Targets added successfully.');
            }
            return redirect()->back()->with('error', 'No valid targets provided.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred.');
        }
    }

    public function edit(EditDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $id = $request->input('id');
        $discount = $discountRepo->getDiscountById($id);

        // Load small reference lists for selects (id + name)
        $categories = Categories::all()->map(function ($c) {
            return (object)['id' => $c->id, 'name' => $c->getTranslation('category-name', 'en')];
        })->values();

        $subcategories = SubCategories::all()->map(function ($s) {
            return (object)['id' => $s->id, 'name' => $s->getTranslation('sub-category-name', 'en')];
        })->values();

        $brands = Brand::all()->map(function ($b) {
            return (object)['id' => $b->id, 'name' => $b->getTranslation('name', 'en')];
        })->values();

        $products = Product::all()->map(function ($p) {
            return (object)['id' => $p->id, 'name' => $p->getTranslation('name', 'en')];
        })->values();

        if (!$discount) {
            return redirect()->route('admin.discounts.index')->with('error', 'Discount not found.');
        }

        // Resolve target names for the edit view (handle product variants)
        $resolvedTargets = $this->formatTargetsCollection($discount->targets);

        // Also pass variants for selection
        $productVariants = ProductVariant::with('product')->get()->map(function ($v) {
            $productName = $v->product ? $v->product->getTranslation('name', 'en') : null;
            $variantName = method_exists($v, 'getTranslation') ? $v->getTranslation('variant_name', 'en') : ($v->variant_name ?? null);
            $label = $productName ? ($productName . ' / ' . ($variantName ?: $v->sku)) : ($variantName ?: $v->sku ?: ('Variant ' . $v->id));
            return (object)['id' => $v->id, 'name' => $label];
        })->values();

        return view('admin.pages.discounts.edit', compact('discount', 'categories', 'subcategories', 'brands', 'products', 'productVariants', 'resolvedTargets'));
    }

    // FIXED: Used UpdateDiscountRequest to validate data, not just ID
    public function update(UpdateDiscountRequest $request, DiscountRepo $discountRepo, DiscountTargetRepo $discountTargetRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            // --- Smart Fill for 'name' translation ---
            if (isset($data['name']['en'])) {
                if (empty($data['name']['fr'])) {
                    $data['name']['fr'] = gtrans($data['name']['en'], 'fr');
                }
                if (empty($data['name']['ar'])) {
                    $data['name']['ar'] = gtrans($data['name']['en'], 'ar');
                }
            }
            // ----------------------------------------

            // Use the validated value for status (prepareForValidation sets 0/1).
            // Avoid using `has()` which can return true after prepareForValidation merges the key.
            $data['status'] = (bool) ($data['status'] ?? 0);
            $discountRepo->update($id, $data);
            if ($request->has('targets')) {
                $discountTargetRepo->assignTargetsToDiscount($id, $request->input('targets', []));
            }
            return redirect()->route('admin.discounts.index')->with('success', 'Discount updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error updating discount.');
        }
    }

    public function destroy(DestroyDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $discountRepo->delete($request->input('id'));
        return redirect()->route('admin.discounts.index')->with('success', 'Discount deleted.');
    }

    public function toggleStatus(Request $request, DiscountRepo $discountRepo, DiscountTargetRepo $discountTargetRepo)
    {
        try {
            $id = $request->input('id');
            $discount = $discountRepo->updateStatus($id);
            return response()->json([
                'success' => true,
                'message' => 'Status updated',
                'status' => $discount ? $discount->status : null,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            Log::error('DiscountController.toggleStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Yajra DataTable
     */
    public function datatable(ReadDiscountRequest $request, DiscountRepo $discountRepo)
    {
        $query = $discountRepo->getDiscountsForDataTable();

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('name', function ($discount) {
                return $discount->getTranslation('name', 'en');
            })
            ->addColumn('starts_at', function ($discount) {
                return $discount->starts_at ? $discount->starts_at->format('Y-m-d') : '';
            })
            ->addColumn('ends_at', function ($discount) {
                return $discount->ends_at ? $discount->ends_at->format('Y-m-d') : '';
            })
            ->addColumn('targets', function ($discount) {
                $items = $discount->targets()->with('target')->get();
                return $this->formatTargetsCollection($items);
            })
            ->addColumn('status', function ($discount) use ($request) {
                $data['toggle_url'] = $request->user() && $request->user()->can('discount.update') ? route('admin.discounts.toggleStatus', ['id' => $discount->id]) : '';
                $data['status'] = $discount->status ? 'active' : 'inactive';
                $data['id'] = $discount->id;
                return view('admin.partials.datatable-status', $data)->render();
            })
            ->addColumn('action', function ($discount) use ($request) {
                $data['edit_url'] = $request->user() && $request->user()->can('discount.update') ? route('admin.discounts.edit', ['id' => $discount->id]) : '';
                $data['delete_url'] = $request->user() && $request->user()->can('discount.delete') ? route('admin.discounts.destroy', ['id' => $discount->id]) : '';
                $data['id'] = $discount->id;
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}
