<?php

namespace App\Http\Requests\Admin\SubCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateSubCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('subcategory.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:sub-categories,id',

            'sub-category-name' => 'required|array',
            'sub-category-name.en' => [
                'required',
                'string',
                'max:255',
            ],
            'sub-category-name.fr' => 'nullable|string|max:255',
            'sub-category-name.ar' => 'nullable|string|max:255',

            'category_id' => 'required|exists:categories,id',
            'sub-category-icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'sub-category-name.en.required' => 'The English sub-category name is required.',
            'sub-category-name.en.unique' => 'The English sub-category name has already been taken.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $enName = $this->input('sub-category-name.en');
            $id = $this->input('id');
            if ($enName) {
                $exists = \App\Models\SubCategories::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(`sub-category-name`, '$.en')) = ?", [$enName])
                    ->where('id', '!=', $id)
                    ->exists();
                if ($exists) {
                    $validator->errors()->add('sub-category-name.en', 'The Sub-category name has already been taken.');
                }
            }
        });
    }
}
