<?php

namespace App\Http\Requests\Admin\Coupon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('coupon.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $couponId = $this->input('id');

        return [
            'id' => 'required|exists:coupons,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code,' . $couponId,
            'coupon_type' => 'required|in:discount,free_shipping,flat_amount',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'code.unique' => 'The coupon code has already been taken.',
            'ends_at.after_or_equal' => 'The end date must be a date after or equal to the start date.',
            'usage_limit_per_user.integer' => 'The usage limit per user must be an integer.',
            'usage_limit.integer' => 'The usage limit must be an integer.',
            'starts_at.date' => 'The start date is not a valid date.',
            'ends_at.date' => 'The end date is not a valid date.',
            'min_order_amount.numeric' => 'The minimum order amount must be a number.',
            'max_discount.numeric' => 'The maximum discount must be a number.',
            'value.numeric' => 'The value must be a number.',
            'starts_at.after_or_equal' => 'The start date must be a date after or equal to the current date.',
            'min_order_amount.min' => 'The minimum order amount must be at least 0.',
            'max_discount.min' => 'The maximum discount must be at least 0.',
            'value.min' => 'The value must be at least 0.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'coupon name',
            'code' => 'coupon code',
            'coupon_type' => 'coupon type',
            'value' => 'coupon value',
            'min_order_amount' => 'minimum order amount',
            'max_discount' => 'maximum discount',
            'usage_limit' => 'usage limit',
            'usage_limit_per_user' => 'usage limit per user',
            'starts_at' => 'start date',
            'ends_at' => 'end date',
            'status' => 'status',
        ];
    }
}
