<?php

namespace App\Repositories\Admin\Repositories;
use App\Models\CouponUsage;
use App\Repositories\Admin\Interfaces\CouponUsageRepositoryInterface;

class CouponUsageRepository implements CouponUsageRepositoryInterface
{
    public function getDataForDataTable($request)
    {
        $filters = is_array($request) ? $request : [];

        $query = CouponUsage::with(['coupon', 'user', 'order'])
            ->select('coupon_usages.*');

        // Apply filters if provided
        if (isset($filters['coupon_id'])) {
            $query->where('coupon_usages.coupon_id', $filters['coupon_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('coupon_usages.user_id', $filters['user_id']);
        }

        if (isset($filters['order_id'])) {
            $query->where('coupon_usages.order_id', $filters['order_id']);
        }

        if (isset($filters['used_from']) && isset($filters['used_to'])) {
            $query->whereBetween('coupon_usages.used_at', [$filters['used_from'], $filters['used_to']]);
        } elseif (isset($filters['used_from'])) {
            $query->where('coupon_usages.used_at', '>=', $filters['used_from']);
        } elseif (isset($filters['used_to'])) {
            $query->where('coupon_usages.used_at', '<=', $filters['used_to']);
        }

        return $query;
    }
}
