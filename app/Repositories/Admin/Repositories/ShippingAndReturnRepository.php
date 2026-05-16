<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\ShippingAndReturns;
use App\Repositories\Admin\Interfaces\ShippingAndReturnRepositoryInterface;

class ShippingAndReturnRepository implements ShippingAndReturnRepositoryInterface
{
    public function getAllShippingAndReturns()
    {
        return ShippingAndReturns::all();
    }

    public function getActiveShippingAndReturns()
    {
        return ShippingAndReturns::where('status', 'active')->get();
    }

    public function getShippingAndReturnById($id)
    {
        return ShippingAndReturns::findOrFail($id);
    }

    public function createShippingAndReturn($request, $data = null)
    {
        // Use passed data (from controller with translations) or fallback to validated request
        $data = $data ?? $request->validated();

        // Ensure status is set correctly if not already handled by Request prepareForValidation
        if (!isset($data['status'])) {
            $data['status'] = 'inactive';
        }

        return ShippingAndReturns::create($data);
    }

    public function updateShippingAndReturn($id, $request, $data = null)
    {
        $shippingAndReturn = ShippingAndReturns::findOrFail($id);

        // Use passed data (from controller with translations) or fallback
        $data = $data ?? $request->validated();

        $shippingAndReturn->update($data);

        return $shippingAndReturn;
    }

    public function updateStatus($id)
    {
        $shippingAndReturn = ShippingAndReturns::findOrFail($id);
        $shippingAndReturn->status = $shippingAndReturn->status === 'active' ? 'inactive' : 'active';
        $shippingAndReturn->save();

        return $shippingAndReturn;
    }

    public function getDataForDataTable()
    {
        return ShippingAndReturns::query()->latest();
    }

    public function deleteShippingAndReturn($id)
    {
        return ShippingAndReturns::findOrFail($id)->delete();
    }
}
