<?php

namespace App\Http\Requests\Admin\Discounts;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiscountTargetRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:discount_targets,id',
            'discount_id' => 'required|exists:discounts,id',
            'target_type' => 'required|in:brand,category,subcategory,product',
            'target_id' => 'required|integer',
            'status' => 'sometimes|boolean',
        ];
    }
}
