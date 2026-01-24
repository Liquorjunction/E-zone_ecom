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
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';
        $data['store_id'] = 'TEMP-' . uniqid();

        if ($request->hasFile('store_logo')) {
            $logoPath = $request->file('store_logo')->store('store_logos', 'public');
            $data['store_logo'] = $logoPath;
        }

        $store = Store::create($data);

        $storeName = strtoupper(str_replace(' ', '-', $data['store_name']['en']));
        $store->store_id = 'STORE-' . $storeName . '-' . $store->id;
        $store->save();

        return $store;
    }

    public function updateStore($id, $request, $data = null)
    {
        $store = Store::findOrFail($id);
        $data = $data ?? $request->validated();
        $data['status'] = in_array($data['status'], ['active', 'inactive']) ? $data['status'] : 'active';

        if ($request->hasFile('store_logo')) {
            if ($store->store_logo && Storage::disk('public')->exists($store->store_logo)) {
                Storage::disk('public')->delete($store->store_logo);
            }
            $logoPath = $request->file('store_logo')->store('store_logos', 'public');
            $data['store_logo'] = $logoPath;
        }

        $store->update($data);
        return $store;
    }

    public function deleteStore($id)
    {
        $store = Store::findOrFail($id);

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
