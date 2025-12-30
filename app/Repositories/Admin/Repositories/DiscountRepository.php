<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Discount;
use App\Repositories\Admin\Interfaces\DiscountRepositoryInterface;

class DiscountRepository implements DiscountRepositoryInterface
{
    public function all()
    {
        return Discount::all();
    }

    public function find($id)
    {
        return Discount::find($id);
    }

    public function create(array $data)
    {
        return Discount::create($data);
    }

    public function update($id, array $data)
    {
        $discount = Discount::find($id);
        if ($discount) {
            $discount->update($data);
            return $discount;
        }
        return false;
    }

    public function delete($id)
    {
        $discount = Discount::find($id);
        if ($discount) {
            // Detach/Delete targets first if strictly needed,
            // but cascading database deletes usually handle this.
            $discount->targets()->delete();
            return $discount->delete();
        }
        return false;
    }

    public function getDiscountById($id)
    {
        return Discount::with('targets.target')->find($id);
    }

    public function updateStatus($id)
    {
        $discount = Discount::find($id);
        if ($discount) {
            $discount->status = !$discount->status;
            $discount->save();
            return $discount;
        }
        return false;
    }

    /**
     * Return Query Builder for DataTables
     */
    public function getDiscountsForDataTable(array $filters = [])
    {
        return Discount::query()->with(['targets']);
    }
}
