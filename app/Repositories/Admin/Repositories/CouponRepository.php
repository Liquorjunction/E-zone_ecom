<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Coupon;
use App\Repositories\Admin\Interfaces\CouponRepositoryInterface;

class CouponRepository implements CouponRepositoryInterface
{
    public function getAllCoupons($filters = [])
    {
        $query = Coupon::query();

        // Apply filters if provided
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['code'])) {
            $query->where('code', 'like', '%' . $filters['code'] . '%');
        }

        return $query->get();
    }

    public function getActiveCoupons()
    {
        return Coupon::where('status', 'active')->get();
    }

    public function getCouponById($id)
    {
        return Coupon::find($id);
    }

    public function storeCoupon($request , $data = null)
    {
        $payload = $this->buildPayload($request, $data);

        return Coupon::create($payload);
    }

    public function updateCoupon($request, $id, $data = null)
    {
        $coupon = Coupon::findOrFail($id);
        $payload = $this->buildPayload($request, $data);

        $coupon->update($payload);
        return $coupon;
    }

    public function deleteCoupon($id)
    {
        $coupon = Coupon::findOrFail($id);
        return $coupon->delete();
    }

    public function updateStatus($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->status = !$coupon->status;
        $coupon->save();
        return $coupon;
    }

    public function getDataForDataTable($filters = [])
    {
        $query = Coupon::query()
            ->select(['id', 'name', 'code', 'coupon_type', 'starts_at', 'ends_at', 'status'])->withCount('usages');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * Normalize input from either FormRequest or raw array.
     */
    private function buildPayload($requestOrArray, $data = null): array
    {
        $payload = [];

        if (is_array($data)) {
            $payload = $data;
        } elseif (is_array($requestOrArray)) {
            $payload = $requestOrArray;
        } elseif (is_object($requestOrArray) && method_exists($requestOrArray, 'validated')) {
            $payload = $requestOrArray->validated();
        }

        // Ensure status is set; favor explicit request input when available
        if (is_object($requestOrArray) && method_exists($requestOrArray, 'input')) {
            $payload['status'] = $requestOrArray->input('status', false);
        } elseif (! array_key_exists('status', $payload)) {
            $payload['status'] = false;
        }

        return $payload;
    }

}
