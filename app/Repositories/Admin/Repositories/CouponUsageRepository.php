<?php

namespace App\Repositories\Admin\Repositories;
use App\Models\CouponUsage;
use App\Repositories\Admin\Interfaces\CouponUsageRepositoryInterface;

class CouponUsageRepository implements CouponUsageRepositoryInterface
{
    public function getDataForDataTable($filters = [])
    {
        $query = CouponUsage::with(['coupon', 'user', 'order'])
            ->select('coupon_usages.*');

        // Apply filters if provided
        if (isset($filters['coupon_id'])) {
            $query->where('coupon_usages.coupon_id', $filters['coupon_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('coupon_usages.user_id', $filters['user_id']);
        }

        if (isset($filters['used_from']) && isset($filters['used_to'])) {
            $query->whereBetween('coupon_usages.used_at', [$filters['used_from'], $filters['used_to']]);
        }

        return $query->get()->map(function ($usage) {
            return [
                'id' => $usage->id,
                'coupon_name' => $usage->coupon?->name,
                'coupon_code' => $usage->coupon?->code,
                'user_name' => $usage->user?->name,
                'user_email' => $usage->user?->email,
                'order_id' => $usage->order?->id,
                'used_at' => $usage->used_at,
            ];
        });
    }
}
