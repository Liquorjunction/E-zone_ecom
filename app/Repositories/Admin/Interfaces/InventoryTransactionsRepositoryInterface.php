<?php

namespace App\Repositories\Admin\Interfaces;

interface InventoryTransactionsRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function getByBatchNo(string $batchNo);
    public function getdataforDataTable(array $filters = []);
}
