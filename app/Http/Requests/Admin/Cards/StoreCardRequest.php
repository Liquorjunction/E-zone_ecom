<?php

namespace App\Http\Requests\Admin\Cards;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('card.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'link' => 'nullable|url|max:255',
            'link_text' => 'nullable|string|max:50',
            'tags' => 'nullable|json',
            'status' => 'nullable',
            'sort_order' => 'nullable|integer',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'The card title field is required.',
            'title.max' => 'The card title may not be greater than 255 characters.',
            'description.required' => 'The card description field is required.',
            'description.max' => 'The card description may not be greater than 255 characters.',
            'image.required' => 'The card image field is required.',
            'image.image' => 'The card image must be an image.',
            'image.mimes' => 'The card image must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'image.max' => 'The card image may not be greater than 5MB.',
            'link.url' => 'The card link must be a valid URL.',
            'link.max' => 'The card link may not be greater than 255 characters.',
            'link_text.max' => 'The card link text may not be greater than 50 characters.',
            'tags.json' => 'The card tags must be a valid JSON.',
            'status.required' => 'The card status field is required.',
            'sort_order.integer' => 'The card sort order must be an integer.',
            'sort_order.min' => 'The card sort order must be greater than or equal to 0.',
            'sort_order.max' => 'The card sort order must be less than or equal to 999.',
            'sort_order.unique' => 'The card sort order has already been taken.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'title' => 'card title',
            'description' => 'card description',
            'image' => 'card image',
            'link' => 'card link',
            'link_text' => 'card link text',
            'tags' => 'card tags',
            'status' => 'card status',
            'sort_order' => 'card sort order',
        ];
    }
}
