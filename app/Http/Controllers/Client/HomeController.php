<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\HomeRepositoryInterface as HomeRepo;
use App\Repositories\Client\Interfaces\QuickStatRepositoryInterface as QuickStatRepo;
use App\Repositories\Client\Interfaces\WhyShopWithUsRepositoryInterface as WhyShopWithUsRepo;
use App\Services\DiscountService;

class HomeController extends Controller
{
    public function index(HomeRepo $homeRepo, QuickStatRepo $quickStatRepo, WhyShopWithUsRepo $whyShopWithUsRepo, DiscountService $discountService)
    {
        $data = $homeRepo->getHomePageData();
        $data['quickStats'] = $quickStatRepo->getActiveQuickStats();
        $data['whyShopWithUs'] = $whyShopWithUsRepo->getAllActiveWhyShopWithUs();
        $data['discounts'] = $discountService->getActiveDiscounts();

        return view('client.pages.home', $data);
    }

    public function dashboard(HomeRepo $homeRepo)
    {
        $data = $homeRepo->getDashboardData();

        return view('client.pages.dashboard.dashboard', $data);
    }

    public function orders(HomeRepo $homeRepo)
    {
        $orders = $homeRepo->getUserOrders(10);

        return view('client.pages.dashboard.orders', compact('orders'));
    }
}
