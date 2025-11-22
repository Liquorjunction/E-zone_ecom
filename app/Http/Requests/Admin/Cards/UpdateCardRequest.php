<?php

namespace App\Http\Requests\Admin\Cards;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('card.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:cards,id',

            // Translatable Fields (Arrays)
            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.fr' => 'nullable|string|max:255',
            'title.ar' => 'nullable|string|max:255',

            'description' => 'nullable|array',
            'description.en' => 'nullable|string', // Removed max:255 as DB column is now TEXT
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',

            'link_text' => 'nullable|array',
            'link_text.en' => 'nullable|string|max:50',
            'link_text.fr' => 'nullable|string|max:50',
            'link_text.ar' => 'nullable|string|max:50',

            // Standard Fields
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'link'        => 'nullable|url|max:255',
            'tags'        => 'nullable|json',
            'status'      => 'required|in:active,inactive',
            'sort_order'  => 'nullable|integer|min:0|max:999',
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
            'id.required' => 'The card id field is required.',
            'title.en.required' => 'The English title is required.',
            'status.in' => 'The selected status is invalid.',
            'sort_order.integer' => 'The sort order must be an integer.',
            'sort_order.min' => 'The sort order must be greater than or equal to 0.',
            'sort_order.max' => 'The sort order must be less than or equal to 999.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'image.max' => 'The image may not be greater than 5MB.',
            'link.url' => 'The link must be a valid URL.',
            'link.max' => 'The link may not be greater than 255 characters.',
            'link_text.max' => 'The link text may not be greater than 50 characters.',
            'tags.json' => 'The tags must be a valid JSON.',

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
        ];
    }
}
