<?php

namespace App\Http\Requests\Admin\ShippingAndReturns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateShippingAndReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('shipping_return.update');
    }

    public function rules(): array
    {
        return [
            'id' => 'required|exists:shipping_and_returns,id',

            'title' => 'required|array',
            'title.en' => 'required|string|max:255',
            'title.fr' => 'nullable|string|max:255',
            'title.ar' => 'nullable|string|max:255',

            'content' => 'required|array',
            'content.en' => 'required|string',
            'content.fr' => 'nullable|string',
            'content.ar' => 'nullable|string',

            'status' => 'nullable|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'title.en.required' => 'The English title is required.',
            'content.en.required' => 'The English content is required.',
        ];
    }

    protected function prepareForValidation()
    {
        // Merge Route ID if missing in body (fixes Edit page bug)
        if ($this->route('shipping_return') && !$this->has('id')) {
            $this->merge(['id' => $this->route('shipping_return')]);
        }
        elseif ($this->route('id') && !$this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }

        // Handle Status Checkbox
        if ($this->has('status')) {
            $this->merge([
                'status' => $this->status === 'on' || $this->status === '1' || $this->status === 'active' ? 'active' : 'inactive',
            ]);
        } else {
            $this->merge(['status' => 'inactive']);
        }
    }
}
