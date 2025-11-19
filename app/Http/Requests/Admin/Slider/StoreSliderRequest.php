<?php

namespace App\Http\Requests\Admin\Slider;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSliderRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'subtitle'    => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|url|max:255',
            // Image is required on creation
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'is_active'   => 'required|boolean',
            'sort_order'  => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'     => 'The slider title is required.',
            'image.required'     => 'Please upload a hero image.',
            'image.max'          => 'The image size must not exceed 5MB.',
            'button_link.url'    => 'The button link must be a valid URL.',
            'is_active.required' => 'Please select a status.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => 'Title',
            'subtitle'    => 'Subtitle',
            'button_text' => 'Button Label',
            'button_link' => 'Button Link',
            'image'       => 'Hero Image',
            'is_active'   => 'Status',
            'sort_order'  => 'Display Order',
        ];
    }

    /**
     * Prepare the data for validation.
     * This handles the conversion if your form sends 'active'/'inactive' strings instead of 1/0.
     */
    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $this->merge([
                'is_active' => $this->status === 'active' ? 1 : 0,
            ]);
        }
    }
}
