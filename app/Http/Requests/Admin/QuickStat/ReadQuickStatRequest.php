<?php

namespace App\Http\Requests\Admin\QuickStat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ReadQuickStatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('quick_stats.read');
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'exists:quick_stats,id'],
        ];
    }
}
