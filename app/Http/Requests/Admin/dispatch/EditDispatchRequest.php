<?php

namespace App\Http\Requests\Admin\dispatch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditDispatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('dispatch.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:order_dispatch,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Dispatch ID is required.',
            'id.exists' => 'The specified dispatch does not exist.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'dispatch ID',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id') ?? $this->input('id'),
        ]);
    }
}
