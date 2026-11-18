<?php

namespace App\Repositories\Client\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function createUser(array $data): User;
    public function findUserByEmail(string $email): ?User;
    public function findUserByPhone(string $phone): ?User;
    public function authenticateUser(array $credentials, bool $remember): bool;
}
