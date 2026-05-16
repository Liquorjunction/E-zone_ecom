<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\InventoryTransaction;
use App\Repositories\Admin\Interfaces\InventoryTransactionsRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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

    public function create(array $data): InventoryTransaction
    {
        return  InventoryTransaction::create($data);
    }

    public function getByBatchNo(string $batchNo): Collection
    {
        return InventoryTransaction::where('batch_no', $batchNo)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getAllTransactionTypes(): Collection
    {
        return InventoryTransaction::select('type')
            ->distinct()
            ->get()
            ->pluck('type');
    }

    public function getdataforDataTable(array $filters = []): Builder
    {
        $query = InventoryTransaction::query()
            ->select([
                'inventory_transactions.id',
                'inventory_transactions.type',
                'inventory_transactions.quantity',
                'inventory_transactions.created_at',
                'inventory_transactions.batch_no',
                'inventory_transactions.reference_type',
                'inventory_transactions.before_quantity',
                'inventory_transactions.after_quantity',
                'inventory_transactions.quantity_change',
                'inventory_transactions.description',
                'products.name as product_name',
                'product_variants.variant_name as variant_name',
                'inventories.batch_no as inventory_batch_no',
                'inventories.location_type as location_type'
            ])
            ->join('inventories', 'inventory_transactions.inventory_id', '=', 'inventories.id')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'inventories.product_variant_id', '=', 'product_variants.id');

        if (!empty($filters['type'])) {
            $query->where('inventory_transactions.type', $filters['type']);
        }

        if (!empty($filters['product'])) {
            $product = trim((string) $filters['product']);
            $query->where(function (Builder $subQuery) use ($product) {
                $subQuery->where('products.name', 'like', "%{$product}%")
                    ->orWhere('product_variants.variant_name', 'like', "%{$product}%")
                    ->orWhere('inventory_transactions.batch_no', 'like', "%{$product}%");
            });
        }

        if (!empty($filters['location_type'])) {
            $query->where('inventories.location_type', $filters['location_type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('inventory_transactions.created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('inventory_transactions.created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    public function getByReference(string $referenceType, int $referenceId, string $type)
    {
        return InventoryTransaction::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->where('type', $type)
            ->get();
    }
}
