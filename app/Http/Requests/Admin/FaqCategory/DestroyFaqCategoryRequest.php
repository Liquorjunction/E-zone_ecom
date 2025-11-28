<?php

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyFaqCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('faqs.delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'id'   => 'required',
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'Id is required',
        ];
    }
    public function attributes()
    {
        return [
            'id'   => 'Id',
        ];
    }
}
