<?php

namespace App\Http\Requests\Client\Compare;

use Illuminate\Foundation\Http\FormRequest;

class AddCompareRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_variant_id' => 'required|integer|exists:product_variants,id',
        ];
    }
}
