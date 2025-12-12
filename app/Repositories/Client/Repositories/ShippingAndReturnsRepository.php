<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\ShippingAndReturnsRepositoryInterface;
use App\Models\ShippingandReturns;

class ShippingAndReturnsRepository implements ShippingAndReturnsRepositoryInterface
{
    public function getActiveShippingAndReturnsData()
    {
        return ShippingandReturns::query()
            ->when(method_exists(ShippingandReturns::class, 'scopeActive'), function ($q) {
                $q->active();
            })
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();
    }
}
