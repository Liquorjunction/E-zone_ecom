<?php

namespace App\Http\Requests\Admin\Cards;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadCardRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('card.read');
    }
}
