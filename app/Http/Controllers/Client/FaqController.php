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
        try {
            // Fetch Active Categories that have Active FAQs
            $categories = FaqCategory::with(['faqs' => function($query) {
                    $query->where('status', 'active');
                }])
                ->where('status', 'active')
                ->whereHas('faqs', function($query) {
                    $query->where('status', 'active'); // Only get categories that actually have FAQs
                })
                ->get();

            return view('client.pages.faq', compact('categories'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Unable to fetch FAQs.']);
        }
    }
}
