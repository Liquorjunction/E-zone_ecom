<?php

namespace App\Repositories\Client\Repositories;

use App\Models\User;
use App\Repositories\Client\Interfaces\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
    {
    public function readProfile(User $user)
    {
        return $user;
    }

    public function editProfile(User $user)
    {
        return $user;
    }

    public function updateProfile(User $user, array $data)
    {
        if (!$user) {
            return null;
        }

        // Update only the provided fields
        $updatableFields = ['name', 'email', 'phone', 'avatar', 'is_newsletter_subscribed'];
        foreach ($updatableFields as $field) {
            if (array_key_exists($field, $data)) {
                $user->$field = $data[$field];
            }
        }

        $user->save();
        return $user;
    }
}
