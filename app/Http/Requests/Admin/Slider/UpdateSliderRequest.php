<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('slider.update');
    }

    public function rules(): array
    {
        return [
            'id'          => 'required|exists:hero_slider,id',
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|url|max:255',
            // Image is OPTIONAL on update
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'is_active'   => 'required|boolean',
            'sort_order'  => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'     => 'The slider title is required.',
            'image.max'          => 'The image size must not exceed 5MB.',
            'button_link.url'    => 'The button link must be a valid URL.',
            'is_active.required' => 'Please select a status.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id'          => 'Slider ID',
            'title'       => 'Title',
            'subtitle'    => 'Subtitle',
            'button_text' => 'Button Label',
            'button_link' => 'Button Link',
            'image'       => 'Hero Image',
            'is_active'   => 'Status',
            'sort_order'  => 'Display Order',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $this->merge([
                'is_active' => $this->status === 'active' ? 1 : 0,
            ]);
        }
    }
}
