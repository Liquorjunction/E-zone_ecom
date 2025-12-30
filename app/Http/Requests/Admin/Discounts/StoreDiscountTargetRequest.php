<?php

namespace App\Http\Requests\Admin\Discounts;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountTargetRequest extends FormRequest
{
    public function authorize()
    {
        // Add your authorization logic if needed
        return true;
    }

    public function rules()
    {
        return [
            'discount_id' => 'required|exists:discounts,id',
            'target_type' => 'required|in:brand,category,subcategory,product',
            'target_id' => 'required|integer',
            'status' => 'sometimes|boolean',
        ];
    }
}
