<?php

namespace App\Http\Requests\Admin\Tnc;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditTncRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('tnc.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:terms_and_conditions,id',
        ];
    }

    /**
     * Prepare the data for validation.
     * This fixes the issue where 'id' is in the URL but not in the request body.
     */
    protected function prepareForValidation()
    {
        // If 'id' is missing from input but exists in route (e.g. /tnc/{id}/edit)
        if ($this->route('tnc') && ! $this->has('id')) {
            $this->merge(['id' => $this->route('tnc')]);
        }
        // Sometimes route parameter is named 'id'
        elseif ($this->route('id') && ! $this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}
