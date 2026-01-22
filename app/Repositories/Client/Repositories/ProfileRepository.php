<?php

namespace App\Repositories\Client\Repositories;

use App\Models\User;
use App\Repositories\Client\Interfaces\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
    {
    public function readProfile(User $user)
    {
        // Get order count
        $orderCount = $user->orders()->count();
        // Get reward points (if you have a field or relation, adjust accordingly)
        $rewardPoints = $user->reward_points ?? 0;
        // Return as array for controller
        return [
            'user' => $user,
            'orderCount' => $orderCount,
            'rewardPoints' => $rewardPoints,
        ];
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
        $updatableFields = ['name', 'email', 'phone', 'is_newsletter_subscribed'];
        foreach ($updatableFields as $field) {
            if (array_key_exists($field, $data)) {
                $user->$field = $data[$field];
            }
        }

        // Handle avatar upload
        if (array_key_exists('avatar', $data)) {
            $imagePath = $data['avatar']->store('avatars', 'public');
            $user->avatar = $imagePath;
        }

        $user->save();
        return $user;
    }
}
