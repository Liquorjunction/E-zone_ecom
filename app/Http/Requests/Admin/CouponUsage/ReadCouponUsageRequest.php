<?php

namespace App\Http\Requests\Admin\CouponUsage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadCouponUsageRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('coupon.read');
    }

    public function rules()
    {
        return [
            'id' => 'nullable|integer|exists:coupon_usages,id',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'order_id' => 'nullable|integer|exists:orders,id',
            'used_from' => 'nullable|date',
            'used_to' => 'nullable|date|after_or_equal:used_from',
        ];
    }
}
