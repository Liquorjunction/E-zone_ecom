<?php

namespace App\Repositories\Client\Interfaces;

use App\Models\User;

interface ProfileRepositoryInterface
{
    public function readProfile(User $user);
    public function editProfile(User $user);
    public function updateProfile(User $user, array $data);
}
