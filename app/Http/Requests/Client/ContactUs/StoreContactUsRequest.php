<?php

namespace App\Http\Requests\Client\ContactUs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreContactUsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
            ],

            'mobile' => [
                'required',
                'string',
                'regex:/^[+]?[\d\s\-()]{7,20}$/',
            ],

            'subject' => [
                'required',
                'in:product_inquiry,order_status,return_replacement,warranty_claim,installation_support,technical_issue,bulk_purchase,complaint,other',
            ],

            'message' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'order_number' => [
                'nullable',
                'required_if:subject,order_status,return_replacement,warranty_claim,complaint',
                'string',
                'max:50',
            ],

            'product_sku' => [
                'nullable',
                'required_if:subject,product_inquiry,installation_support,technical_issue,warranty_claim',
                'string',
                'max:100',
            ],

            'attachments' => [
                'nullable',
                'array',
                'max:5',
            ],

            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,gif,webp,heic,heif,pdf,doc,docx,xls,xlsx',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Please enter your full name.',
            'name.min'                  => 'Name must be at least 2 characters.',
            'name.max'                  => 'Name must not exceed 100 characters.',

            'email.required'            => 'Please enter your email address.',
            'email.email'               => 'Please enter a valid email address.',

            'mobile.required'           => 'Please enter your mobile number.',
            'mobile.regex'              => 'Please enter a valid mobile number.',

            'subject.required'          => 'Please select a subject.',
            'subject.in'                => 'The selected subject is invalid.',

            'message.required'          => 'Please enter your message.',
            'message.min'               => 'Message must be at least 10 characters.',
            'message.max'               => 'Message must not exceed 5000 characters.',

            'order_number.required_if'  => 'Please enter your order number for this type of request.',
            'product_sku.required_if'   => 'Please enter the product SKU / model number for this type of request.',

            'attachments.max'           => 'You may upload a maximum of 5 files.',
            'attachments.*.file'        => 'Each attachment must be a valid file.',
            'attachments.*.max'         => 'Each file must not exceed 10 MB.',
            'attachments.*.mimes'       => 'Allowed file types: JPG, PNG, GIF, WebP, HEIC, PDF, DOC, DOCX, XLS, XLSX.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name'         => 'full name',
            'email'        => 'email address',
            'mobile'       => 'mobile number',
            'subject'      => 'subject',
            'message'      => 'message',
            'order_number' => 'order number',
            'product_sku'  => 'product SKU',
            'attachments'  => 'attachments',
            'attachments.*' => 'attachment',
        ];
    }
}
