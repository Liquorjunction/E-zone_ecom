<?php

namespace App\Repositories\Admin\Repositories;

use App\Models\Store;
use App\Repositories\Admin\Interfaces\StoreRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class StoreRepository implements StoreRepositoryInterface
{
    public function getAllStores()
    {
        return Store::orderBy('created_at', 'desc')->get();
    }

    public function getAllActiveStores()
    {
        return Store::where('status', 'active')->orderBy('created_at', 'desc')->get();
    }

    public function getStoreById($id)
    {
        return Store::findOrFail($id);
    }

    public function createStore($request, $data = null)
    {
        $data = $data ?? $request->validated();

        // Ensure status is valid enum value
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';

        // Assign temporary Store ID (will be updated after creation)
        $data['store_id'] = 'TEMP-' . uniqid();

        // Handle store_logo upload if present
        if ($request->hasFile('store_logo')) {
            $logoPath = $request->file('store_logo')->store('store_logos', 'public');
            $data['store_logo'] = $logoPath;
        }

        // Create store first to get the ID
        $store = Store::create($data);

        // Generate store_id based on store name (English) and ID
        $storeName = strtoupper(str_replace(' ', '-', $data['store_name']['en']));
        $store->store_id = 'STORE-' . $storeName . '-' . $store->id;
        $store->save();

        return $store;
    }

    public function updateStore($id, $request, $data = null)
    {
        $store = Store::findOrFail($id);
        $data = $data ?? $request->validated();

        // Ensure status is valid enum value
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';

        // Handle store_logo update if present
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            if ($store->store_logo && Storage::disk('public')->exists($store->store_logo)) {
                Storage::disk('public')->delete($store->store_logo);
            }

            // Store new logo
            $logoPath = $request->file('store_logo')->store('store_logos', 'public');
            $data['store_logo'] = $logoPath;
        }

        $store->update($data);
        return $store;
    }

    public function deleteStore($id)
    {
        $store = Store::findOrFail($id);

        // Delete store logo if exists
        if ($store->store_logo && Storage::disk('public')->exists($store->store_logo)) {
            Storage::disk('public')->delete($store->store_logo);
        }

        return $store->delete();
    }

    public function updateStatus($id)
    {
        $store = Store::findOrFail($id);
        $store->status = ($store->status === 'active') ? 'inactive' : 'active';
        $store->save();

        return $store;
    }

    public function getDataForDataTable()
    {
        return Store::withCount('inventories')->latest();
    }

}
