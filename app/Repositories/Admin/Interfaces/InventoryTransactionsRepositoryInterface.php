<?php

namespace App\Repositories\Admin\Interfaces;

interface InventoryTransactionsRepositoryInterface
{
    public function all(array $filters = []);
    public function find(int $id);
}
