<?php

namespace App\Http\Requests\Admin\Store;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('store.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:stores,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'The store ID is required.',
            'id.exists'   => 'The specified store does not exist.',
        ];
    }

    public function attributes()
    {
        return [
            'id' => 'Store ID',
        ];
    }
}
