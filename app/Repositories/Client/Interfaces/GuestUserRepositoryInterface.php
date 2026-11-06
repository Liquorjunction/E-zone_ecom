<?php

namespace App\Repositories\Client\Interfaces;

use App\Models\GuestUser;
use App\Models\User;

interface GuestUserRepositoryInterface
{
    public function findByEmail(string $email): ?GuestUser;

    public function findByToken(string $token): ?GuestUser;

    public function findById(int $id): ?GuestUser;

    public function createOrUpdateGuest(array $data): GuestUser;

    public function linkGuestOrdersToUser(User $user): int;
}
