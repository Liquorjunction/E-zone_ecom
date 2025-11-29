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
        $query = FaqCategory::query();
        return $query->latest();
    }


}
