<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use App\Models\Categories;
use App\Models\Card;
use App\Models\ProductVariant;
use App\Models\Brand;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Hero Sliders
        $sliders = HeroSlider::orderBy('sort_order', 'asc')->where('is_active', '1')->get();

        // 2. Categories (For the Main Grid)
        $categories = Categories::where('status', 'active')
                                ->withCount('subcategories')
                                ->get();

        // 3. Promo Cards (Replacing the static Gaming/Smart Home section)
        $cards = Card::where('status', 'active')
                     ->orderBy('sort_order', 'asc')
                     ->get();

        // 4. New Arrivals: fetch latest product variants tied to recently created products
        // We join products to order variants by parent product creation date and take 8
        $newArrivals = ProductVariant::select('product_variants.*')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.status', 'active')
            ->where('product_variants.status', 'active')
            ->with(['product', 'images'])
            ->orderBy('products.created_at', 'desc')
            ->take(8)
            ->get();

        // 5. Bestsellers: fetch product variants for products flagged as bestseller
        $bestSellers = ProductVariant::select('product_variants.*')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('products.status', 'active')
            ->where('products.is_bestseller', 1)
            ->where('product_variants.status', 'active')
            ->with(['product', 'images'])
            ->orderBy('products.updated_at', 'desc')
            ->take(8)
            ->get();

        // 6. Brands
        $brands = Brand::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('client.pages.home', compact('sliders', 'categories', 'cards', 'newArrivals', 'bestSellers', 'brands'));
    }
}
