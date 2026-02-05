<?php

namespace App\Http\Requests\Admin\Newsletter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditNewsletterRequest extends FormRequest
{
    public function authorize()
    {
        return Auth::guard('admin')->user()->can('newsletter.update');
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:newsletter,id',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->route('id') && !$this->has('id')) {
            $this->merge(['id' => $this->route('id')]);
        }
        elseif ($this->route('newsletter') && !$this->has('id')) {
            $this->merge(['id' => $this->route('newsletter')]);
        }
    }
}
