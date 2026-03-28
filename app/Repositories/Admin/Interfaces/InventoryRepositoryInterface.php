<?php

namespace App\Repositories\Admin\Interfaces;

use App\Models\OrderItem;

interface InventoryRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function deleteByBatchNo(string $batchNo);
    public function getByBatchNo(string $batchNo);
    public function getForReservation(int $productId, ?int $variantId, string $orderDirection = 'asc');
    public function getLatestInward(int $productId, ?int $variantId);
    public function isBatchSold(string $batchNo);
    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null);
    public function findWithLock(int $id);
    public function getBatchesForDataTable(array $filters = []);
    public function getLowStock(array $filters = []);
    public function getLockedForCheckout(int $productId, ?int $variantId);
    public function getInventoryForOrderItem(OrderItem $orderItem);
}
