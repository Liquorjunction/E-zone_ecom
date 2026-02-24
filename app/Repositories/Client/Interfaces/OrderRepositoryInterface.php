<?php

namespace App\Repositories\Client\Interfaces;

use App\Models\Cart;
use App\Services\PriceCalculatorService;

interface OrderRepositoryInterface
{
    public function create(array $data);
    public function createItem(array $data);
    public function update(int $orderId, array $data);
    public function cancelOrder(int $orderId, ?string $reason = null);
    public function getOrdersWithRelations(array $filters, array $relations = [], int $perPage = 10);
    public function findOrderWithRelations(int $orderId, array $relations = []);
    public function getLatestOrderForUser(int $userId, array $relations = []);
    public function findOrder(int $orderId);
}
