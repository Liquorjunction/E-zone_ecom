<?php

namespace App\Http\Requests\Admin\Cards;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyCardRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('card.delete');
    }

    public function rules()
    {
        return [
            'id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Card ID is required.',
            'id.exists' => 'Card not found.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Card ID',
        ];
    }
}
