<?php

namespace App\Repositories\Client\Interfaces;

interface SecurityRepositoryInterface
{
    public function getSessions(int $userId);
    public function countSessions(int $userId): int;
    public function validateCurrentPassword($user, string $currentPassword): bool;
    public function getLastPasswordUpdateTime(int $userId);
    public function updatePassword($user, string $newPassword): void;
    public function revokeSession(string $sessionId, int $userId): void;
    public function logoutAllSessions(int $userId): void;
}
