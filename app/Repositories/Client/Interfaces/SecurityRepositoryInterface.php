<?php

namespace App\Repositories\Client\Interfaces;

interface SecurityRepositoryInterface
{
    public function getSessions(int $userId);
    public function updatePassword($user, string $newPassword): void;
    public function revokeSession(string $sessionId, int $userId): void;
    public function logoutAllSessions(int $userId): void;
}
