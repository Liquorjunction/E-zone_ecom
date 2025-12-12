<?php

namespace App\Http\Requests\Admin\Showrooms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyShowroomRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('showrooms.delete');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:showrooms,id',
        ];
    }

    public function prepareForValidation()
    {
        if ($this->route('id') && !$this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
        elseif ($this->route('showroom') && !$this->has('id')) {
            $this->merge(['id' => $this->route('showroom')]);
        }
    }
}
