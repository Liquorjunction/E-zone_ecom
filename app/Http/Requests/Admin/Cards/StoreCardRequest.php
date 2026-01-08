<?php

namespace App\Http\Requests\Admin\Cards;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCardRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('card.create');
    }

    public function rules()
    {
        return [
            // Title validation
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.fr' => 'nullable|string|max:255',
            'title.ar' => 'nullable|string|max:255',

            // Description validation
            'description' => 'required|array',
            'description.en' => 'required|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            // Link Text validation
            'link_text' => 'required|array',
            'link_text.en' => 'required|string|max:50',

            // Common Fields
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'link' => 'required|url|max:255',
            'tags' => 'nullable|json',

            // Status accepts 'active' via checkbox value or 'inactive' if not present
            'status' => 'nullable',
            'sort_order' => 'nullable|integer|min:0|max:999',
        ];
    }

    public function messages()
    {
        return [
            'title.en.required' => 'The English title is required.',
            'description.en.required' => 'The English description is required.',
            'image.required' => 'Please upload a card image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'image.max' => 'The image may not be greater than 5MB.',
            'link_text.required' => 'Please enter the link text.',
            'link_text.en.required' => 'The English link text is required.',
            'link_text.en.max' => 'The English link text may not be greater than 50 characters.',
            'link.required' => 'Please enter a link.',
            'link.url' => 'The link must be a valid URL.',
            'link.max' => 'The link may not be greater than 255 characters.',
            'link_text.max' => 'The link text may not be greater than 50 characters.',
            'tags.json' => 'The tags must be a valid JSON.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.min' => 'The sort order must be greater than or equal to 0.',
            'sort_order.max' => 'The sort order must be less than or equal to 999.',
        ];
    }

    public function attributes()
    {
        return [
            'title.en' => 'English Title',
            'title.fr' => 'French Title',
            'title.ar' => 'Arabic Title',
            'description.en' => 'English Description',
            'description.fr' => 'French Description',
            'description.ar' => 'Arabic Description',
            'link_text.en' => 'English Link Text',
            'link_text.fr' => 'French Link Text',
            'link_text.ar' => 'Arabic Link Text',
            'image' => 'Image',
            'link' => 'Link',
            'tags' => 'Tags',
            'status' => 'Status',
            'sort_order' => 'Sort Order',
        ];
    }
}
