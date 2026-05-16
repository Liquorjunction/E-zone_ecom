<?php

namespace App\Http\Requests\Client\Wishlist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddWishlistRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_variant_id' => 'required|exists:product_variants,id',
        ];
    }
}
