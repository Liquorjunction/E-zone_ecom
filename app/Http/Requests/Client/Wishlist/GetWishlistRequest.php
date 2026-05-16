<?php

namespace App\Http\Requests\Client\Wishlist;

use Illuminate\Foundation\Http\FormRequest;

class GetWishlistRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [];
    }
}
