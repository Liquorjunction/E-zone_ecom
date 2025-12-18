<?php

namespace App\Repositories\Client\Interfaces;

interface AddressRepositoryInterface
{
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function setDefaultAddress(int $userId, int $addressId);
    public function getDefaultAddress(int $userId);
    public function getAddressesByUserId(int $userId);
}
