<?php

namespace App\Repositories\Client\Interfaces;

use App\DTOs\CartIdentifier;
use App\Models\Cart;
use Illuminate\Http\Request;

interface CartRepositoryInterface
{
    public function getCartForUserOrSession(CartIdentifier $identifier);
    public function addItem($cart, array $data);
    public function updateItem($cart, int $itemId, array $data);
    public function removeItem($cart, int $itemId);
    public function clearCart($cart);
    public function calculateCartTotals(Cart $cart): array;
    public function getOrderDataByOrderId(int $orderId);
    public function getAllOrdersForUser($userId);
}
