<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\FaqCategory;
use App\Repositories\Admin\Interfaces\FaqCategoryRepositoryInterface;

class FaqCategoryRepository implements FaqCategoryRepositoryInterface
{
    public function getAllFaqCategories()
    {
        return FaqCategory::all();
    }

    public function getActiveFaqCategories()
    {
        return FaqCategory::where('status', 'active')->get();
    }

    public function getFaqCategoryById($id)
    {
        return FaqCategory::findOrFail($id);
    }

    public function createFaqCategory($request, $data = null)
    {
        // Use passed data (with translations) or fallback to validated request data
        $data = $data ?? $request->validated();

        return FaqCategory::create($data);
    }

    public function updateFaqCategory($request, $id, $data = null)
    {
        $faqCategory = FaqCategory::findOrFail($id);
        $data = $data ?? $request->validated();

        $faqCategory->update($data);

        return $faqCategory;
    }

    public function deleteFaqCategory($id)
    {
        return FaqCategory::findOrFail($id)->delete();
    }

    public function updateFaqCategoryStatus($id)
    {
        $faqCategory = FaqCategory::findOrFail($id);
        $faqCategory->status = $faqCategory->status === 'active' ? 'inactive' : 'active';
        $faqCategory->save();

        return $faqCategory;
    }

    public function getDataforDataTable($request = null)
    {
        $query = FaqCategory::withCount('faqs')->latest();

        $getInput = function (string $key) use ($request) {
            if (is_array($request)) {
                return $request[$key] ?? null;
            }

            if (is_object($request) && method_exists($request, 'input')) {
                return $request->input($key);
            }

            return null;
        };

        // Status filter
        $status = $getInput('status');
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        // Date range filter
        $startDate = $getInput('start_date') ?? $getInput('date_from');
        $endDate = $getInput('end_date') ?? $getInput('date_to');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query;
    }
}
