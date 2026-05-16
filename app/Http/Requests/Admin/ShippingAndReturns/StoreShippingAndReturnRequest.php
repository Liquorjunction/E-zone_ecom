<?php

namespace App\Http\Requests\Admin\ShippingAndReturns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreShippingAndReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('shipping_return.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Title Validation (Array for translations)
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.fr' => 'nullable|string|max:255',
            'title.ar' => 'nullable|string|max:255',

            // Content Validation (Array for translations, allows HTML/long text)
            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.fr' => 'nullable|string',
            'content.ar' => 'nullable|string',

            // Status Validation
            'status' => 'nullable|in:active,inactive',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.en.required' => 'The English title is required.',
            'content.en.required' => 'The English content is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Handles checkbox 'status' conversion if necessary.
     */
    protected function prepareForValidation()
    {
        if ($this->has('status')) {
            $this->merge([
                'status' => $this->status === 'on' || $this->status === '1' ? 'active' : 'inactive',
            ]);
        } else {
            $this->merge(['status' => 'inactive']);
        }
    }
}
