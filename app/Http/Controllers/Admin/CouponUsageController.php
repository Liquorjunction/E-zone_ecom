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
            $couponUsages = $couponUsageRepo->getDataForDataTable($request->all());

            return DataTables::of($couponUsages)
                ->addIndexColumn()
                ->filterColumn('coupon_code', function ($query, $keyword) {
                    $query->where(function ($searchQuery) use ($keyword) {
                        $searchQuery->whereHas('coupon', function ($couponQuery) use ($keyword) {
                            $couponQuery->where('code', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('user', function ($userQuery) use ($keyword) {
                            $userQuery->where('name', 'like', "%{$keyword}%")
                                ->orWhere('email', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('order', function ($orderQuery) use ($keyword) {
                            $orderQuery->where('id', 'like', "%{$keyword}%");
                        });
                    });
                })
                ->addColumn('coupon_name', function ($usage) {
                    return $usage->coupon?->name ?? 'N/A';
                })
                ->addColumn('coupon_code', function ($usage) {
                    return strtoupper($usage->coupon?->code ?? 'N/A');
                })
                ->addColumn('user_name', function ($usage) {
                    return $usage->user?->name ?? 'N/A';
                })
                ->addColumn('user_email', function ($usage) {
                    return $usage->user?->email ?? 'N/A';
                })
                ->addColumn('order_id', function ($usage) {
                    return $usage->order?->id ?? 'N/A';
                })
                ->addColumn('used_at', function ($usage) {
                    return $usage->used_at ? \Carbon\Carbon::parse($usage->used_at)->toDateTimeString() : 'N/A';
                })
                ->rawColumns([])
                ->make(true);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => 'Failed to fetch data for DataTable.'], 500);
        }
    }
}
