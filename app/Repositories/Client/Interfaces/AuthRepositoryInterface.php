<?php

namespace App\Repositories\Client\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthRepositoryInterface
{
    public function createUser(array $data): User;
    public function findUserByEmail(string $email): ?User;
    public function authenticateUser(array $credentials, bool $remember): bool;
}
