<?php

namespace App\Http\Requests\Admin\Brands;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('brands.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'name.en' => ['required', Rule::unique('brands', 'name->en')],
            'name.ar' => 'nullable',
            'name.fr' => 'nullable',
            'tagline' => 'nullable|array',
            'tagline.en' => 'nullable|string',
            'tagline.fr' => 'nullable|string',
            'tagline.ar' => 'nullable|string',
            'description' => 'nullable|array',
            'description.en' => 'nullable|string',
            'description.fr' => 'nullable|string',
            'description.ar' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'required|in:active,inactive',

            // Banners
            'banners' => 'required|array|min:1',
            'banners.*.id' => 'nullable|integer',
            'banners.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'banners.*.alt_text' => 'nullable|array',
            'banners.*.sort_order' => 'nullable|integer',
            'banners.*.status' => 'nullable|in:active,inactive',
            'banners.*._delete' => 'nullable',

            // Showcases
            'showcases' => 'nullable|array',
            'showcases.*.id' => 'nullable|integer',
            'showcases.*.category_id' => 'nullable',
            'showcases.*.sub_category_id' => 'nullable',
            'showcases.*.title' => 'nullable|array',
            'showcases.*.title.en' => 'nullable|string|max:255',
            'showcases.*.title.fr' => 'nullable|string|max:255',
            'showcases.*.title.ar' => 'nullable|string|max:255',
            'showcases.*.description' => 'nullable|array',
            'showcases.*.button_text' => 'nullable|array',
            'showcases.*.button_text.en' => 'nullable|string|max:255',
            'showcases.*.button_text.fr' => 'nullable|string|max:255',
            'showcases.*.button_text.ar' => 'nullable|string|max:255',
            'showcases.*.button_url' => 'nullable|string|max:500',
            'showcases.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:5120',
            'showcases.*.sort_order' => 'nullable|integer',
            'showcases.*.status' => 'nullable|in:active,inactive',
            'showcases.*._delete' => 'nullable',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate Banners
            $banners = $this->input('banners', []);
            $activeBannersCount = 0;
            if (is_array($banners)) {
                foreach ($banners as $index => $banner) {
                    if (!empty($banner['_delete']) && $banner['_delete'] == '1') {
                        continue;
                    }
                    $activeBannersCount++;

                    if (!$this->hasFile("banners.{$index}.image")) {
                        $validator->errors()->add(
                            "banners.{$index}.image",
                            "The banner image is required for banner slide #" . ($index + 1) . "."
                        );
                    }
                }
            }

            if ($activeBannersCount < 1) {
                $validator->errors()->add('banners', 'At least 1 hero banner slide is mandatory.');
            }

            // Validate Showcases
            $showcases = $this->input('showcases', []);
            if (is_array($showcases)) {
                foreach ($showcases as $index => $showcase) {
                    if (!empty($showcase['_delete']) && $showcase['_delete'] == '1') {
                        continue;
                    }

                    // Card Name (title.en) is mandatory
                    if (empty($showcase['title']['en']) || trim((string)($showcase['title']['en'] ?? '')) === '') {
                        $validator->errors()->add(
                            "showcases.{$index}.title.en",
                            "The card name (Title EN) is required for showcase card #" . ($index + 1) . "."
                        );
                    }

                    // Image is mandatory
                    if (!$this->hasFile("showcases.{$index}.image")) {
                        $validator->errors()->add(
                            "showcases.{$index}.image",
                            "The lifestyle photo is required for showcase card #" . ($index + 1) . "."
                        );
                    }

                    // Button URL is mandatory if Button Text is entered
                    $hasBtnText = !empty(trim((string)($showcase['button_text']['en'] ?? ''))) ||
                                  !empty(trim((string)($showcase['button_text']['fr'] ?? ''))) ||
                                  !empty(trim((string)($showcase['button_text']['ar'] ?? '')));

                    if ($hasBtnText && empty(trim((string)($showcase['button_url'] ?? '')))) {
                        $validator->errors()->add(
                            "showcases.{$index}.button_url",
                            "The Button URL is required for showcase card #" . ($index + 1) . " when Button Text is provided."
                        );
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'name.en.unique' => 'Name must be unique',
            'name.required' => 'Name is required',
            'image.required' => 'Image is required',
            'status.required' => 'Status is required',
            'showcases.*.image.image' => 'Showcase photo must be a valid image.',
            'showcases.*.image.mimes' => 'Showcase photo must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'showcases.*.image.max' => 'Showcase photo may not be greater than 5MB.',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Name',
            'name.en' => 'Name (en)',
            'image' => 'Image',
            'status' => 'Status',
        ];
    }
}
