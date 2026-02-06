<?php

namespace App\Repositories\Client\Repositories;

use App\Models\HeroSlider;
use App\Models\Categories;
use App\Models\Card;
use App\Models\ProductVariant;
use App\Models\Brand;
use App\Repositories\Client\Interfaces\HomeRepositoryInterface as HomeRepositoryInterface;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class HomeRepository implements HomeRepositoryInterface
{
    public function getHomePageData()
    {
        $sliders = HeroSlider::orderBy('sort_order', 'asc')->where('is_active', '1')->get();

        $categories = Categories::where('status', 'active')
                                ->withCount('subcategories')
                                ->get();

        $cards = Card::where('status', 'active')
                     ->orderBy('sort_order', 'asc')
                     ->get();

        $newArrivals = ProductVariant::where('status', 'active')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['product', 'images', 'reviews' => function ($query) {
                $query->where('is_approved', 'approved');
            }])
            ->get()
            ->sortByDesc(function ($variant) {
                return optional($variant->product)->created_at;
            })
            ->take(12)
            ->values();

        $bestSellers = ProductVariant::where('is_bestseller', 1)
            ->where('status', 'active')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['product', 'images', 'reviews' => function ($query) {
                $query->where('is_approved', 'approved');
            }])
            ->orderBy('updated_at', 'desc')
            ->take(8)
            ->get();

        $brands = Brand::where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        // Fetch discounted product variants (deals) using DiscountService
        $allVariants = ProductVariant::where('status', 'active')
            ->whereHas('product', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['product', 'images', 'reviews' => function ($query) {
                $query->where('is_approved', 'approved');
            }])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Use DiscountService to filter only discounted variants
        $discountService = app(\App\Services\DiscountService::class);
        $deals = $allVariants->filter(function($variant) use ($discountService) {
            $discount = $discountService->calculateVariantDiscount($variant);
            return $discount['has_discount'] && $discount['discount_amount'] > 0;
        })->take(8)->values();

        $wishlistVariantIds = collect([]);
        $userId = Auth::id();
        if ($userId) {
            $wishlistVariantIds = Wishlist::where('user_id', $userId)->pluck('product_variant_id')->filter()->values();
        }

        return compact('sliders', 'categories', 'cards', 'newArrivals', 'bestSellers', 'brands', 'wishlistVariantIds', 'deals');
    }

    public function getDashboardData()
    {
        $userId = Auth::id();

        if (! $userId) {
            return [
                'orders' => collect([]),
                'ordersCount' => 0,
                'wishlistCount' => 0,
                'addressesCount' => 0,
            ];
        }

        $ordersQuery = Order::where('user_id', $userId)->orderBy('created_at', 'desc');

        $orders = $ordersQuery->with(['items.product', 'items.variant'])->take(5)->get();

        $ordersCount = $ordersQuery->count();

        $wishlistCount = Wishlist::where('user_id', $userId)->count();

        $addressesCount = Address::where('user_id', $userId)->count();

        return compact('orders', 'ordersCount', 'wishlistCount', 'addressesCount');
    }

    public function getUserOrders(int $perPage = 10): LengthAwarePaginator
    {
        $userId = Auth::id();

        return Order::where('user_id', $userId)
            ->with(['items.product', 'items.variant'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
