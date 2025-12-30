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

class DiscountController extends Controller
{
    public function index()
    {
        return view('admin.pages.discounts.index');
    }

    /**
     * Show the unified create form.
     */
    public function create()
    {
        return view('admin.pages.discounts.create');
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
                            'status'      => isset($target['status']) ? 1 : 0,
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
        $discount = $discountRepo->getDiscountById($request->input('id'));
        if (!$discount) {
            return redirect()->back()->with('error', 'Discount not found.');
        }
        return view('admin.pages.discounts.edit', compact('discount'));
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

            $data['status'] = $request->has('status');
            $discountRepo->update($id, $data);
            if($request->has('targets')) {
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
            if ($discount) {
                // Update children to match parent
                $discountTargetRepo->setStatusByDiscountId($id, $discount->status);
            }
            return response()->json([
                'success' => true,
                'message' => 'Status updated',
                'status' => $discount ? $discount->status : null,
                'id' => $id
            ]);
        } catch (\Exception $e) {
            Log::error('DiscountController.toggleStatus: '.$e->getMessage());
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
                $targets = $discount->targets->take(3)->map(function($target) {
                    $name = $target->target ? ($target->target->name ?? 'Unknown') : 'N/A';
                    $badgeClass = $target->status ? 'badge-info' : 'badge-secondary';
                    return '<span class="badge '.$badgeClass.' me-1">'.ucfirst($target->target_type).': '.$name.'</span>';
                })->implode('');
                if ($discount->targets->count() > 3) {
                    $targets .= ' <span class="badge badge-light">+'.($discount->targets->count() - 3).' more</span>';
                }
                return $targets;
            })
            ->addColumn('status', function ($discount) use ($request) {
                $data['toggle_url'] = $request->user() && $request->user()->can('discounts.update') ? route('admin.discounts.toggleStatus', ['id' => $discount->id]) : '';
                $data['status'] = $discount->status ? 'active' : 'inactive';
                $data['id'] = $discount->id;
                return view('admin.partials.datatable-status', $data)->render();
            })
            ->addColumn('action', function ($discount) use ($request) {
                $data['edit_url'] = $request->user() && $request->user()->can('discounts.update') ? route('admin.discounts.edit', ['id' => $discount->id]) : '';
                $data['delete_url'] = $request->user() && $request->user()->can('discounts.delete') ? route('admin.discounts.destroy', ['id' => $discount->id]) : '';
                $data['id'] = $discount->id;
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['status', 'targets', 'action'])
            ->make(true);
    }
}
