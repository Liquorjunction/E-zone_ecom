<?php

namespace App\Http\Requests\Client\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CartAddRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => ['required','integer','exists:products,id'],
            'variant_id' => ['nullable','integer','exists:product_variants,id'],
            'quantity' => ['nullable','integer','min:1']
        ];
    }
}
