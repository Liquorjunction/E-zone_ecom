<?php

namespace App\Http\Requests\Admin\QuickStat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DestroyQuickStatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->user()->can('quick_stats.delete');
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:quick_stats,id'],
        ];
    }
}
