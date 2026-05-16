<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coupon\StoreCouponRequest;
use App\Http\Requests\Admin\Coupon\UpdateCouponRequest;
use App\Http\Requests\Admin\Coupon\DestroyCouponRequest;
use App\Http\Requests\Admin\Coupon\ReadCouponRequest;
use App\Http\Requests\Admin\Coupon\EditCouponRequest;
use App\Models\Coupon;
use App\Repositories\Admin\Interfaces\CouponRepositoryInterface as CouponRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function index()
    {
        return view('admin.pages.coupon.index');
    }

    public function create()
    {
        return view('admin.pages.coupon.create');
    }

    public function store(StoreCouponRequest $request, CouponRepo $couponRepo)
    {
        try {
            $data = $request->validated();

            $couponRepo->storeCoupon($data);

            return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(ReadCouponRequest $request, CouponRepo $couponRepo)
    {
        $id = $request->input('id');
        $coupon = $couponRepo->getCouponById($id);

        return view('admin.pages.coupon.show', compact('coupon'));
    }

    public function edit(EditCouponRequest $request, CouponRepo $couponRepo)
    {
        $id = $request->input('id');
        $coupon = $couponRepo->getCouponById($id);

        return view('admin.pages.coupon.edit', compact('coupon'));
    }

    public function update(UpdateCouponRequest $request, CouponRepo $couponRepo)
    {
        try {
            $id = $request->input('id');
            $data = $request->validated();

            $couponRepo->updateCoupon($request, $id, $data);

            return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(DestroyCouponRequest $request, CouponRepo $couponRepo)
    {
        try {
            $id = $request->input('id');
            $couponRepo->deleteCoupon($id);

            return response()->json([
                'success' => true,
                'message' => 'Coupon deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(EditCouponRequest $request, CouponRepo $couponRepo)
    {
        try {
            $id = $request->input('id');
            $status = $request->input('status');

            $couponRepo->updateStatus($id, $status);

            return response()->json(['success' => true, 'message' => 'Coupon status updated successfully.']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getCouponTypeList(CouponRepo $couponRepo)
    {
        try {
            $types = $couponRepo->getCouponTypeList();

            return response()->json(['success' => true, 'data' => $types]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function data(ReadCouponRequest $request, CouponRepo $couponRepo)
    {
        $coupons = $couponRepo->getDataForDataTable($request->all());

        return DataTables::of($coupons)
            ->addIndexColumn()
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%$keyword%")
                ->orWhere('code', 'like', "%$keyword%");
            })
            ->addColumn('name', function ($coupon) {
                return ucfirst($coupon->name);
            })
            ->addColumn('code', function ($coupon) {
                return strtoupper($coupon->code);
            })
            ->addColumn('usages_count', function ($coupon) {
                return $coupon->usages_count;
            })
            ->addColumn('coupon_type', function ($coupon) {
                return ucfirst($coupon->coupon_type);
            })
            ->addColumn('start_date', function ($coupon) {
                return optional($coupon->starts_at)->toDateTimeString();
            })
            ->addColumn('end_date', function ($coupon) {
                return optional($coupon->ends_at)->toDateTimeString();
            })
            ->addColumn('status', function ($coupon) use ($request) {
                if ($request->user()->can('coupon.update')) {
                    $data['toggle_url'] = $request->user()->can('coupon.update') ? route('admin.coupons.updateStatus', ['id' => $coupon->id]) : '';
                    $data['status'] = $coupon->status ? 'active' : 'inactive';
                    $data['id'] = $coupon->id;
                    return view('admin.partials.datatable-status', $data)->render();
                } else {
                    return view('components.datatable-status-badge', ['status' => $coupon->status])->render();
                }
            })
            ->addColumn('action', function ($coupon) use ($request) {
                $data['id'] = $coupon->id;
                $data['view_url'] = $request->user() && $request->user()->can('coupon.read') ? route('admin.coupons.show', ['id' => $coupon->id]) : '';
                if ($request->user()->can('coupon.update') && isset($coupon->usages_count) && $coupon->usages_count == 0) {
                    $data['edit_url'] = $request->user() && $request->user()->can('coupon.update') ? route('admin.coupons.edit', ['id' => $coupon->id]) : '';
                } else {
                    $data['edit_url'] = '';
                }
                if ($request->user()->can('coupon.delete') && isset($coupon->usages_count) && $coupon->usages_count == 0) {
                    $data['delete_url'] = $request->user() && $request->user()->can('coupon.delete') ? route('admin.coupons.destroy', ['id' => $coupon->id]) : '';
                } else {
                    $data['delete_url'] = '';
                }
                return view('admin.partials.datatable-actions', $data)->render();
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}
