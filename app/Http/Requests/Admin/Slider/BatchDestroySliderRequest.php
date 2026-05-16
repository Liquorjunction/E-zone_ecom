<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BatchDestroySliderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('slider.delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:hero_slider,id',
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => 'The slider IDs are required.',
            'ids.array'    => 'The slider IDs must be an array.',
            'ids.*.exists' => 'One or more sliders do not exist.',
        ];
    }

    public function attributes()
    {
        return [
            'ids' => 'Slider IDs',
        ];
    }
}
