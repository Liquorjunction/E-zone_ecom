<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Repositories\Admin\Interfaces\FaqRepositoryInterface;

class FaqRepository implements FaqRepositoryInterface
{
    public function getAllFaqs()
    {
        return Faq::all();
    }

    public function getActiveFaqs()
    {
        return Faq::where('status', 'active')->get();
    }

    public function getFaqById($id)
    {
        return Faq::findOrFail($id);
    }

    public function getFaqsByCategory($categoryId)
    {
        return Faq::where('category_id', $categoryId)->get();
    }

    public function createFaq($request, $data = null)
    {
        // Use passed data (with translations) or fallback to validated request data
        $data = $data ?? $request->validated();

        return Faq::create($data);
    }

    public function updateFaq($request, $id, $data = null)
    {
        $faq = Faq::findOrFail($id);
        $data = $data ?? $request->validated();

        $faq->update($data);

        return $faq;
    }

    public function updateFaqStatus($id)
    {
        $faq = Faq::findOrFail($id);
        $faq->status = $faq->status === 'active' ? 'inactive' : 'active';
        $faq->save();

        return $faq;
    }

    public function getFaqCategoryList()
    {
        return FaqCategory::where('status', 'active')->pluck('name', 'id');
    }

    public function getDataForDataTable($requests)
    {
        $query = Faq::query()->latest();

        // Status filter
        $status = is_array($requests) ? ($requests['status'] ?? null) : $requests->input('status');
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        // Date range filter
        $dateFrom = is_array($requests) ? ($requests['date_from'] ?? null) : $requests->input('date_from');
        $dateTo = is_array($requests) ? ($requests['date_to'] ?? null) : $requests->input('date_to');
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        // Category filter
        $categoryId = is_array($requests) ? ($requests['category_id'] ?? null) : $requests->input('category_id');
        if ($categoryId) {
            $query->where('faq_category_id', $categoryId);
        }

        return $query;
    }

    public function deleteFaq($id)
    {
        return Faq::findOrFail($id)->delete();
    }
}
