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
            $query->where('code', $filters['code']);
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

    public function storeCoupon($request, $data = null)
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

        // Apply filters if provided
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply global search filter
        if (isset($filters['search']['value']) && !empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('coupon_type', 'like', "%$search%");
            });
        }

        // Apply ordering
        if (isset($filters['order'][0]['column']) && isset($filters['order'][0]['dir'])) {
            $columns = ['id', 'name', 'code', 'usages_count', 'coupon_type', 'starts_at', 'ends_at', 'status'];
            $orderColumn = $columns[$filters['order'][0]['column']];
            $orderDir = $filters['order'][0]['dir'];
            $query->orderBy($orderColumn, $orderDir);
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
