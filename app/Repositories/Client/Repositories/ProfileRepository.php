<?php

namespace App\Repositories\Client\Repositories;

use App\Models\User;
use App\Repositories\Client\Interfaces\ProfileRepositoryInterface;

class ProfileRepository implements ProfileRepositoryInterface
    {
    public function readProfile(User $user)
    {
        $orderCount = $user->orders()->count();
        $rewardPoints = $user->reward_points ?? 0;
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

        $updatableFields = ['name', 'email', 'phone', 'is_newsletter_subscribed'];
        foreach ($updatableFields as $field) {
            if (array_key_exists($field, $data)) {
                $user->$field = $data[$field];
            }
        }

        if (array_key_exists('avatar', $data)) {
            $imagePath = $data['avatar']->store('avatars', 'public');
            $user->avatar = $imagePath;
        }

        $user->save();
        return $user;
    }
}
