<?php

namespace App\Repositories\Client\Repositories;

use App\Models\ProductVariant;
use App\Repositories\Client\Interfaces\CompareRepositoryInterface;

class CompareRepository implements CompareRepositoryInterface
{
    public function getActiveVariantsByIds(array $ids)
    {
        if (empty($ids)) {
            return collect();
        }

        return ProductVariant::query()
            ->with(['product.brand', 'product.category', 'reviews'])
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->get();
    }

    public function getActiveVariantById(int $id)
    {
        return ProductVariant::query()
            ->with(['product.category'])
            ->whereKey($id)
            ->where('status', 'active')
            ->first();
    }

    public function getActiveIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        return ProductVariant::query()
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
