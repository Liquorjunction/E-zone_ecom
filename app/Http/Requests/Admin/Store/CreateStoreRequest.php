<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('store.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'store_name' => 'required|array',
            'store_name.en' => 'required|string|max:255',
            'store_name.fr' => 'nullable|string|max:255',
            'store_name.ar' => 'nullable|string|max:255',
            'location' => 'nullable|array',
            'location.en' => 'nullable|string',
            'location.fr' => 'nullable|string',
            'location.ar' => 'nullable|string',
            'status' => 'required|in:0,1,active,inactive',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'store_footer' => 'nullable|array',
            'store_footer.en' => 'nullable|string|max:500',
            'store_footer.fr' => 'nullable|string|max:500',
            'store_footer.ar' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'store_name.en.required' => 'The English store name is required.',
            'store_logo.image'       => 'The store logo must be an image file.',
            'store_logo.max'         => 'The store logo size must not exceed 5MB.',
        ];
    }

}
