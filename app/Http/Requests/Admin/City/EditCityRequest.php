<?php 

namespace App\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditCityRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('cities.update');
    }

    public function rules()
    {
        return [
            'id' => 'sometimes|integer',
        ];
    }
}