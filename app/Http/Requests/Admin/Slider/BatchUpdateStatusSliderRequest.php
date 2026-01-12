<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BatchUpdateStatusSliderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('slider.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids'       => 'required|array',
            'ids.*'     => 'integer|exists:hero_slider,id',
            'is_active' => 'required|in:0,1',
        ];
    }

    public function messages()
    {
        return [
            'ids.required'       => 'The slider IDs are required.',
            'ids.array'          => 'The slider IDs must be an array.',
            'ids.*.exists'       => 'One or more sliders do not exist.',
            'is_active.required' => 'The status field is required.',
            'is_active.in'       => 'The status field must be 0 or 1.',
        ];
    }

    public function attributes()
    {
        return [
            'ids'       => 'Slider IDs',
            'is_active' => 'Status',
        ];
    }
}
