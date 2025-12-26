<?php

namespace App\Repositories\Client\Repositories;

use App\Repositories\Client\Interfaces\SecurityRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityRepository implements SecurityRepositoryInterface
{
	public function getSessions(int $userId)
	{
		if (config('session.driver') !== 'database') {
			return [];
		}

		return DB::table('sessions')
			->where('user_id', $userId)
			->orderBy('last_activity', 'desc')
			->get();
	}

	public function updatePassword($user, string $newPassword): void
	{
		$user->password = $newPassword;
		$user->setRememberToken(Str::random(60));
		$user->save();

		if (config('session.driver') === 'database') {
			DB::table('sessions')->where('user_id', $user->id)->delete();
		}
	}

	public function revokeSession(string $sessionId, int $userId): void
	{
		if (config('session.driver') === 'database') {
			DB::table('sessions')->where('id', $sessionId)->where('user_id', $userId)->delete();
		}
	}

	public function logoutAllSessions(int $userId): void
	{
		if (config('session.driver') === 'database') {
			DB::table('sessions')->where('user_id', $userId)->delete();
		}
	}
}

