<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\SecurityRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityRepository implements SecurityRepositoryInterface
{
	public function getSessions(int $userId)
	{
		return DB::table('sessions')
			->where('user_id', $userId)
			->orderBy('last_activity', 'desc')
			->get();
	}

	public function countSessions(int $userId): int
	{
		return (int) DB::table('sessions')
			->where('user_id', $userId)
			->count();
	}

    public function getLastPasswordUpdateTime(int $userId)
    {
        $user = User::find($userId);
        return $user ? $user->updated_at : null;
    }

    public function validateCurrentPassword($user, string $currentPassword): bool
    {
        return Hash::check($currentPassword, $user->password);
    }

	public function updatePassword($user, string $newPassword): void
	{
		$user->password = Hash::make($newPassword);
		$user->setRememberToken(Str::random(60));
		$user->save();
		DB::table('sessions')->where('user_id', $user->id)->delete();
	}

	public function revokeSession(string $sessionId, int $userId): void
	{
		DB::table('sessions')->where('id', $sessionId)->where('user_id', $userId)->delete();
	}

	public function logoutAllSessions(int $userId): void
	{
		DB::table('sessions')->where('user_id', $userId)->delete();
	}
}

