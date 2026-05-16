<?php

namespace App\Http\Requests\Admin\Discounts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('discount.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.fr' => 'nullable|string|max:255',
            'name.ar' => 'nullable|string|max:255',
            'type' => 'required|in:flat,percentage',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === 'percentage' && $value > 100) {
                        $fail('The percentage discount value cannot exceed 100.');
                    }
                },
            ],
            'max_discount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'nullable|boolean',

            // Validate Targets Array - Fixed field names to match controller
            'targets' => 'nullable|array',
            'targets.*.target_type' => 'required_with:targets|in:brand,category,subcategory,product',
            'targets.*.target_id' => 'required_with:targets|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.array' => 'The name must be an array of translations.',
            'name.*.required' => 'Each translation of the name is required.',
            'name.*.string' => 'Each translation of the name must be a string.',
            'name.*.max' => 'Each translation of the name may not be greater than 255 characters.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The selected type is invalid.',
            'value.required' => 'The value field is required.',
            'value.numeric' => 'The value must be a number.',
            'value.min' => 'The value must be at least 0.',
            'max_discount.numeric' => 'The max discount must be a number.',
            'max_discount.min' => 'The max discount must be at least 0.',
            'starts_at.date' => 'The start date is not a valid date.',
            'ends_at.date' => 'The end date is not a valid date.',
            'ends_at.after_or_equal' => 'The end date must be a date after or equal to the start date.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status field must be true or false.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'name',
            'type' => 'type',
            'value' => 'value',
            'max_discount' => 'max discount',
            'starts_at' => 'start date',
            'ends_at' => 'end date',
            'status' => 'status',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            // If 'status' is present (checked), set to 1. If missing (unchecked), set to 0.
            'status' => $this->has('status') ? 1 : 0,
        ]);
    }
}
