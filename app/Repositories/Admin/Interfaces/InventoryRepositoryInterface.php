<?php

namespace App\Repositories\Admin\Interfaces;

interface InventoryRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function updateStatus($id);
    public function delete(int $id);
    public function getDataforDataTable(array $filters = []);

    // Custom queries
    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null);
    public function getLowStock(array $filters = []);
}
