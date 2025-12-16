<?php

namespace App\Repositories\Admin\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InventoryRepositoryInterface
{
    public function createBatch(array $batchData);
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function updateStatus($id);
    public function delete(int $id);
    public function getDataforDataTable(array $filters = []);
    public function findByProduct(int $productId, ?int $variantId = null, ?int $warehouseId = null, ?int $storeId = null);
    public function getLowStock(array $filters = []);
    public function getBatchesForDataTable(array $filters = []);
}
