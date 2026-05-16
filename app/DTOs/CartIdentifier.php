<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartIdentifier
{
    public readonly ?int $userId;
    public readonly string $sessionId;

    public function __construct(?int $userId, string $sessionId)
    {
        $this->userId = $userId;
        $this->sessionId = $sessionId;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            Auth::id(),
            $request->session()->getId()
        );
    }
}
