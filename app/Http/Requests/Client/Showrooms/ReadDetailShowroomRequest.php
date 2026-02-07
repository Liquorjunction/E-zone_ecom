<?php

namespace App\Http\Requests\Client\Showrooms;

use Illuminate\Foundation\Http\FormRequest;

class ReadDetailShowroomRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->route('slug')) {
            $this->merge(['slug' => $this->route('slug')]);
        }
    }

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
            'slug' => 'required|string|exists:showrooms,slug',
        ];
    }
}
