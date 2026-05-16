<?php

namespace App\Http\Requests\Client\Showrooms;

use Illuminate\Foundation\Http\FormRequest;

class ReadShowroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'search' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'per_page' => 'nullable|integer|min:1|max:50',
        ];
    }
}
