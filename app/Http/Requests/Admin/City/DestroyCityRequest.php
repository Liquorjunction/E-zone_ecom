<?php 

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyCityRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('cities.delete');
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|integer',
        ];
    }
}