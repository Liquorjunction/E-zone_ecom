<?php

namespace App\Http\Requests\Admin\WhyShopWithUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditWhyShopWithUs extends FormRequest
{

    public function authorize()
    {
        return Auth::guard('admin')->user()->can('why_choose_us.update');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:why_shop_with_us,id',
        ];
    }
}
