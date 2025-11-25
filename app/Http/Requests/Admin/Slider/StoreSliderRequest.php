<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('slider.create');
    }

    public function rules(): array
    {
        return [
            // Translatable Fields (Arrays)
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.fr' => 'nullable|string|max:255',
            'title.ar' => 'nullable|string|max:255',

            'subtitle' => 'nullable|array',
            'subtitle.en' => 'nullable|string|max:255',
            'subtitle.fr' => 'nullable|string|max:255',
            'subtitle.ar' => 'nullable|string|max:255',

            'button_text' => 'nullable|array',
            'button_text.en' => 'nullable|string|max:50',
            'button_text.fr' => 'nullable|string|max:50',
            'button_text.ar' => 'nullable|string|max:50',

            // Standard Fields
            'button_link' => 'nullable|url|max:255',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'is_active'   => 'required|boolean',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.en.required' => 'The English title is required.',
            'image.required'    => 'Please upload a hero image.',
            'image.max'         => 'The image size must not exceed 5MB.',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $this->merge(['is_active' => $this->status === 'active' ? 1 : 0]);
        }
    }
}
