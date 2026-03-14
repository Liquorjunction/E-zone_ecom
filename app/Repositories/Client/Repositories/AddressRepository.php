<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\AddressRepositoryInterface;
use App\Models\Address;
use Illuminate\Support\Facades\Log;

class AddressRepository implements AddressRepositoryInterface
{
    public function findById(int $id)
    {
        return Address::find($id);
    }

    public function create(array $data)
    {
        Log::debug('Creating new address', ['data' => $data]);
        return Address::create($data);
    }

    public function update(int $id, array $data)
    {
        Log::debug('Updating address', ['id' => $id, 'data' => $data]);
        $address = Address::find($id);
        if ($address) {
            $address->update($data);
        }
        return $address;
    }

    public function delete(int $id)
    {
        $address = Address::find($id);
        if ($address) {
            $address->delete();
            return true;
        }
        return false;
    }

    public function setDefaultAddress(int $userId, int $addressId)
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);
        $address = Address::where('id', $addressId)->where('user_id', $userId)->first();
        if ($address) {
            $address->is_default = true;
            $address->save();
            return $address;
        }
        return null;
    }

    public function getDefaultAddress(int $userId)
    {
        return Address::where('user_id', $userId)->where('is_default', true)->first();
    }

    public function getAddressesByUserId(int $userId)
    {
        return Address::where('user_id', $userId)->get();
    }

    public function findByIdAndUser(int $id, int $userId)
    {
        return Address::where('id', $id)->where('user_id', $userId)->first();
    }

    public function getAddressesByType(int $userId, string $type)
    {
        return Address::where('user_id', $userId)
            ->where(function($q) use ($type) {
                $q->where('type', $type)->orWhereNull('type');
            })
            ->get();
    }

    public function firstOrCreateAddress(array $matchAttributes, array $fillAttributes)
    {
        return Address::firstOrCreate($matchAttributes, $fillAttributes);
    }
}
