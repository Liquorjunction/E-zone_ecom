<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\DiscountTarget;
use App\Repositories\Admin\Interfaces\DiscountTargetRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiscountTargetRepository implements DiscountTargetRepositoryInterface
{
    public function addTargets($discountId, array $targets): bool
    {
        $now = now();
        $dataToInsert = [];

        foreach ($targets as $target) {
            // Normalize the target_type coming from forms (e.g., "Sub_Category", "subcategory")
            $rawType = isset($target['target_type']) ? $target['target_type'] : '';
            $type = strtolower(trim($rawType));
            // replace common separators to match morphMap keys like 'subcategory'
            $type = str_replace([' ', '_', '-'], '', $type);

            $dataToInsert[] = [
                'discount_id' => $discountId,
                'target_type' => $type,
                'target_id'   => $target['target_id'],
                'created_at'  => $now,
                'updated_at'  => $now,
            ];
        }

        try {
            if (!empty($dataToInsert)) {
                DiscountTarget::insert($dataToInsert);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('DiscountTargetRepository.addTargets: ' . $e->getMessage(), ['targets' => $targets]);
            return false;
        }
    }

    public function updateTarget($id, array $data)
    {
        $target = DiscountTarget::find($id);
        if ($target) {
            $target->update($data);
            return $target;
        }
        return null;
    }

    public function deleteTarget($id): bool
    {
        return (bool) DiscountTarget::destroy($id);
    }

    public function getAll(array $filters = [])
    {
        $query = DiscountTarget::query()->with('target');
        if (isset($filters['discount_id'])) {
            $query->where('discount_id', $filters['discount_id']);
        }

        return $query->get();
    }

    public function getById($id)
    {
        return DiscountTarget::with('target')->find($id);
    }

    public function assignTargetsToDiscount($discountId, array $targets): bool
    {
        DB::beginTransaction();
        try {
            DiscountTarget::where('discount_id', $discountId)->delete();
            $this->addTargets($discountId, $targets);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function removeTargetsFromDiscount($discountId, array $targetIds): bool
    {
        return (bool) DiscountTarget::where('discount_id', $discountId)
            ->whereIn('id', $targetIds)
            ->delete();
    }

    // status-related methods removed because `discount_targets` table has no `status` column

    public function getDiscountTargetsForDataTable(array $filters = [])
    {
        // Eager load 'target' to get Product/Brand names
        $query = DiscountTarget::query()->with(['target', 'discount']);

        if (isset($filters['discount_id'])) {
            $query->where('discount_id', $filters['discount_id']);
        }

        return $query;
    }
}
