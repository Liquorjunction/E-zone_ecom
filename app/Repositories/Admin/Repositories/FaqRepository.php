<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Faq;
use App\Repositories\Admin\Interfaces\FaqRepositoryInterface;

class FaqRepository implements FaqRepositoryInterface
{
    public function getAllFaqs()
    {
        return Faq::all();
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

    public function getDataForDataTable()
    {
        return Faq::query()->latest();
    }

    public function deleteFaq($id)
    {
        return Faq::findOrFail($id)->delete();
    }
}
