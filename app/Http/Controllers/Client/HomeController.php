<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Repositories\Client\Interfaces\HomeRepositoryInterface;

class HomeController extends Controller
{
    public function index(HomeRepositoryInterface $homeRepo)
    {
        $data = $homeRepo->getHomePageData();

        return view('client.pages.home', $data);
    }

    public function dashboard(HomeRepositoryInterface $homeRepo)
    {
        $data = $homeRepo->getDashboardData();

        return view('client.pages.dashboard.dashboard', $data);
    }

    public function orders(HomeRepositoryInterface $homeRepo)
    {
        $orders = $homeRepo->getUserOrders(10);

        return view('client.pages.dashboard.orders', compact('orders'));
    }
}
