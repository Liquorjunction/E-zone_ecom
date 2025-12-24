<?php

namespace App\Http\Requests\Client\Wishlist;

use Illuminate\Foundation\Http\FormRequest;

class RemoveWishlistRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
        ];
    }
}
