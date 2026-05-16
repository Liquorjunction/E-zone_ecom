<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\ShippingAndReturnsRepositoryInterface;
use App\Models\ShippingAndReturns;

class ShippingAndReturnsRepository implements ShippingAndReturnsRepositoryInterface
{
    public function getActiveShippingAndReturnsData()
    {
        return ShippingAndReturns::query()
            ->when(method_exists(ShippingAndReturns::class, 'scopeActive'), function ($q) {
                $q->active();
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
    }
}
