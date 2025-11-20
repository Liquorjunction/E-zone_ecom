<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroySliderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('slider.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:hero_slider,id',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'The slider ID is required.',
            'id.exists'   => 'The slider does not exist.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Slider ID',
        ];
    }
}
