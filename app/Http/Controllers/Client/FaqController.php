<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\FaqRepositoryInterface as FaqRepo;
use Illuminate\Http\Request;

class FaqController extends Controller
{

    public function index(FaqRepo $faqRepository)
    {
        try {
            // Fetch Active Categories with Active FAQs using Repository
            $faqcategories = $faqRepository->getActiveFaqCategoriesWithFaqs();

            return view('client.pages.faq', compact('faqcategories'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => statictranslations('Unable to fetch FAQs.')]);
        }
    }
}
