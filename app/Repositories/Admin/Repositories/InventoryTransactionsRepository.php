<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\InventoryTransaction;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class InventoryTransactionsRepository implements InventoryTransactionsRepositoryInterface
{
    public function all(array $filters = []): Collection
    {
        return InventoryTransaction::latest()->get();
    }

    public function find(int $id): ?InventoryTransaction
    {
        return InventoryTransaction::find($id);
    }

    /**
     * Get Data for DataTable using Joins.
     * * We use JOINs so we can sort/search by 'product_name' directly.
     */
    public function getdataforDataTable(array $filters = [])
    {
        $query = InventoryTransaction::query()
            ->select([
                'inventory_transactions.*',
                'products.name as product_name', // Get name from products table
                'inventories.batch_no as inventory_batch_no',
                'inventories.location_type as location_type' // <-- Add this line
            ])
            // 1. Link Transaction to Inventory
            ->join('inventories', 'inventory_transactions.inventory_id', '=', 'inventories.id')
            // 2. Link Inventory to Product
            ->join('products', 'inventories.product_id', '=', 'products.id');

        // Apply Filters if needed
        if (isset($filters['type'])) {
            $query->where('inventory_transactions.type', $filters['type']);
        }

        // Return the Query Builder! Do not use ->get() here.
        // DataTables will handle the limit/offset automatically.
        return $query;
    }
}
