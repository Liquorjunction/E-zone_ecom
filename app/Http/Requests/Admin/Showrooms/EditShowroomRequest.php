<?php

namespace App\Http\Requests\Admin\Showrooms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditShowroomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('showrooms.update');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:showrooms,id',
        ];
    }

    protected function prepareForValidation()
    {
        // Helper to grab ID from URL if not in body (e.g. /showrooms/{id}/edit)
        if ($this->route('showroom') && !$this->has('id')) {
            $this->merge(['id' => $this->route('showroom')]);
        }
        elseif ($this->route('id') && !$this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
    }
}
