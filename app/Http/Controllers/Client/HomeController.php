<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\HomeRepositoryInterface as HomeRepo;

class HomeController extends Controller
{
    public function index(HomeRepo $homeRepo)
    {
        $data = $homeRepo->getHomePageData();

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
