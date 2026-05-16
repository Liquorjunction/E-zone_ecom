<?php

namespace App\Http\Requests\Client\Product;

use Illuminate\Foundation\Http\FormRequest;

class ReadProductRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Client-side, no specific authorization needed for reading
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Add any necessary validation rules for client-side reading
        ];
    }
}
