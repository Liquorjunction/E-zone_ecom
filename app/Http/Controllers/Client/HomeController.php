<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\HomeRepositoryInterface as HomeRepo;
use App\Repositories\Admin\Interfaces\QuickStatRepositoryInterface as QuickStatRepo;

class HomeController extends Controller
{
    public function index(HomeRepo $homeRepo, QuickStatRepo $quickStatRepo)
    {
        $data = $homeRepo->getHomePageData();
        $data['quickStats'] = $quickStatRepo->getActiveQuickStats();

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
