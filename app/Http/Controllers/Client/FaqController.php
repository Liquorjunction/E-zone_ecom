<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        // Get all active FAQ Categories by relationship with active FAQs
        $faqCategories = FaqCategory::whereHas('faqs', function ($query) {
            $query->where('status', 'active');
        })->where('status', 'active')->get();

        // Get all active FAQs by relationship with active FAQ Categories
        $faqs = Faq::whereHas('faqCategory', function ($query) {
            $query->where('status', 'active');
        });
        return view('client.pages.faq', compact('faqCategories', 'faqs'));
    }
}
