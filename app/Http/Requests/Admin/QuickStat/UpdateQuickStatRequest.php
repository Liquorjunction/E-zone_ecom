<?php

namespace App\Http\Requests\Admin\QuickStat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateQuickStatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('quick_stats.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:quick_stats,id',
            'text1' => 'required|array',
            'text1.en' => 'required|string|max:255',
            'text1.ar' => 'nullable|string|max:255',
            'text1.fr' => 'nullable|string|max:255',
            'text2' => 'required|array',
            'text2.en' => 'required|string|max:255',
            'text2.ar' => 'nullable|string|max:255',
            'text2.fr' => 'nullable|string|max:255',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,ico,tiff,avif|max:2048',
            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The quick stat ID is required.',
            'id.exists' => 'The specified quick stat does not exist.',
            'text1.required' => 'The primary text is required.',
            'text1.en.required' => 'The primary text in English is required.',
            'text1.en.max' => 'The primary text in English may not be greater than 255 characters.',
            'text1.ar.max' => 'The primary text in Arabic may not be greater than 255 characters.',
            'text1.fr.max' => 'The primary text in French may not be greater than 255 characters.',
            'text2.required' => 'The secondary text is required.',
            'text2.en.required' => 'The secondary text in English is required.',
            'text2.en.max' => 'The secondary text in English may not be greater than 255 characters.',
            'text2.ar.max' => 'The secondary text in Arabic may not be greater than 255 characters.',
            'text2.fr.max' => 'The secondary text in French may not be greater than 255 characters.',
            'icon.image' => 'The icon must be an image.',
            'icon.mimes' => 'The icon must be a file of type: jpeg, png, jpg, gif, webp, bmp, ico, tiff, avif.',
            'icon.max' => 'The icon may not be greater than 2MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'quick stat ID',
            'text1' => 'primary text',
            'text1.en' => 'primary text in English',
            'text1.ar' => 'primary text in Arabic',
            'text1.fr' => 'primary text in French',
            'text2' => 'secondary text',
            'text2.en' => 'secondary text in English',
            'text2.ar' => 'secondary text in Arabic',
            'text2.fr' => 'secondary text in French',
            'icon' => 'icon',
        ];
    }
}
