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

    // Target formatting moved to repository; controller should use repository methods.

    /**
     * Show the unified create form.
     */
    public function create(DiscountRepo $discountRepo)
    {
        $lists = $discountRepo->getSelectLists();
        $productVariants = $discountRepo->getProductVariants();
        $resolvedTargets = [];
        return view('admin.pages.discounts.create', array_merge($lists, compact('productVariants', 'resolvedTargets')));
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

        // Use repository to fetch select lists and variants
        $lists = $discountRepo->getSelectLists();
        $categories = $lists['categories'] ?? collect();
        $subcategories = $lists['subcategories'] ?? collect();
        $brands = $lists['brands'] ?? collect();
        $products = $lists['products'] ?? collect();

        if (!$discount) {
            return redirect()->route('admin.discounts.index')->with('error', 'Discount not found.');
        }

        // Resolve target names via repository
        $resolvedTargets = $discountRepo->formatTargetsCollection($discount->targets);
        // Also pass variants for selection from repository
        $productVariants = $discountRepo->getProductVariants();

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
            ->addColumn('targets', function ($discount) use ($discountRepo) {
                $items = $discount->targets()->with('target')->get();
                return $discountRepo->formatTargetsCollection($items);
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
