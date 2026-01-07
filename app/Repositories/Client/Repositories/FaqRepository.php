<?php

namespace App\Repositories\Client\Repositories;

use App\Models\FaqCategory;
use App\Repositories\Client\Interfaces\FaqRepositoryInterface;

class FaqRepository implements FaqRepositoryInterface
{
    public function getActiveFaqCategoriesWithFaqs()
    {
        $faqcategory = FaqCategory::where('status', 'active')
            ->with(['faqs' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return $faqcategory;
    }
}
