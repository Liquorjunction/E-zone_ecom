<?php

namespace App\Repositories\Client\Interfaces;

interface HomeRepositoryInterface
{
    public function getHomePageData();
    public function getDashboardData();
    public function getUserOrders(int $perPage = 10);
}
