<?php

namespace App\Http\Requests\Client\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Please enter a coupon code.',
            'code.string' => 'Coupon code must be text.',
            'code.max' => 'Coupon code is too long.',
        ];
    }
}
