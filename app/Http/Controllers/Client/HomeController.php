<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use App\Models\Categories;
use App\Models\Card;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Hero Sliders
        // Temporarily remove is_active filter to fetch all sliders for testing
        $sliders = HeroSlider::orderBy('sort_order', 'asc')->where('is_active', '1')->get();

        // 2. Categories (For the Main Grid)
        $categories = Categories::where('status', 'active')
                                ->withCount('subcategories')
                                ->get();

        // 3. Promo Cards (Replacing the static Gaming/Smart Home section)
        $cards = Card::where('status', 'active')
                     ->orderBy('sort_order', 'asc')
                     ->get();

        return view('client.pages.home', compact('sliders', 'categories', 'cards'));
    }
}
