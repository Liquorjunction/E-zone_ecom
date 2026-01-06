<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponUsage\ReadCouponUsageRequest;
use App\Repositories\Admin\Interfaces\CouponUsageRepositoryInterface as CouponUsageRepo;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class CouponUsageController extends Controller
{
    public function index()
    {
        return view('admin.pages.coupon_usage.index');
    }

    public function data(ReadCouponUsageRequest $request, CouponUsageRepo $couponUsageRepo)
    {
        try {
            $filters = $request->only(['id', 'coupon_id', 'user_id', 'order_id', 'used_from', 'used_to']);
            $couponUsages = $couponUsageRepo->getDataForDataTable($filters);

            return DataTables::of($couponUsages)
                ->addIndexColumn()
                ->addColumn('coupon_code', function ($usage) {
                    return strtoupper($usage['coupon_code'] ?? 'N/A');
                })
                ->addColumn('used_at', function ($usage) {
                    return isset($usage['used_at']) ? \Carbon\Carbon::parse($usage['used_at'])->toDateTimeString() : 'N/A';
                })
                ->rawColumns([])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'Failed to fetch data for DataTable.'], 500);
        }
    }
}
