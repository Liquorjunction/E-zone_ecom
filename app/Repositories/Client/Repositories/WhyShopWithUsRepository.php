<?php

namespace App\Repositories\Client\Repositories;

use App\Models\WhyShopWithUs;
use App\Repositories\Client\Interfaces\WhyShopWithUsRepositoryInterface;

class WhyShopWithUsRepository implements WhyShopWithUsRepositoryInterface
{
    public function getAllActiveWhyShopWithUs()
    {
        return WhyShopWithUs::where('status', 'active')
            ->orderBy('order', 'asc')
            ->get();
    }
}
