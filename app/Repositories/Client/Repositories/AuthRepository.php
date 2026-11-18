<?php

namespace App\Repositories\Client\Repositories;

use App\Models\User;
use App\Repositories\Client\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'email_verified_at' => null,
        ]);
    }

    public function findUserByEmail(string $email): ?User
    {
        $normalized = strtolower(trim($email));
        if ($normalized === '') {
            return null;
        }

        return User::whereRaw('LOWER(email) = ?', [$normalized])->first();
    }

    public function findUserByPhone(string $phone): ?User
    {
        $trimmed = trim($phone);
        if ($trimmed === '') {
            return null;
        }

        $stripped = preg_replace('/[\s\-\(\)]+/', '', $trimmed);
        $digits   = preg_replace('/\D+/', '', $trimmed);

        $candidates = array_filter(array_unique([
            $trimmed,
            $stripped,
            $digits,
            $digits !== '' ? '+' . $digits : '',
        ]));

        // Handle Moroccan local (0XXXXXXXXX) <-> international (+212XXXXXXXXX / 212XXXXXXXXX)
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $local = substr($digits, 1);
            $candidates[] = '+212' . $local;
            $candidates[] = '212' . $local;
        } elseif (strlen($digits) === 12 && str_starts_with($digits, '212')) {
            $candidates[] = '0' . substr($digits, 3);
        }

        $candidates = array_values(array_unique($candidates));
        if (empty($candidates)) {
            return null;
        }

        $placeholders = implode(',', array_fill(0, count($candidates), '?'));

        return User::whereNotNull('phone')
            ->where('phone', '!=', '')
            ->whereRaw(
                "REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '(', ''), ')', '') IN ({$placeholders})",
                $candidates
            )
            ->first();
    }

    public function authenticateUser(array $credentials, bool $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }
}
