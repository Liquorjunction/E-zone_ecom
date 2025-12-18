<?php

namespace App\Services;

use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use Illuminate\Http\Request;
use Psr\Log\LoggerInterface;

class CartService
{
    protected $repo;
    protected $logger;

    public function __construct(CartRepositoryInterface $repo, LoggerInterface $logger)
    {
        $this->repo = $repo;
        $this->logger = $logger;
    }

    public function addItem(Request $request, array $payload)
    {
        $cart = $this->repo->getCartForUserOrSession($request);

        // compute unit price from product/variant if not provided
        $unitPrice = $payload['unit_price'] ?? null;
        if (!$unitPrice) {
            // best-effort lookup, avoid heavy coupling
            try {
                $variant = \App\Models\ProductVariant::find($payload['variant_id'] ?? null);
                $unitPrice = $variant->price ?? $variant->sale_price ?? null;
            } catch (\Throwable $e) {
                $unitPrice = $payload['unit_price'] ?? null;
            }
        }

        $payload['unit_price'] = $unitPrice;

        $item = $this->repo->addItem($cart, $payload);
        $this->logger->info('Cart item added', ['cart_id' => $cart->id ?? null, 'item_id' => $item->id]);
        return $item;
    }

    public function updateItem(Request $request, int $itemId, array $data)
    {
        $cart = $this->repo->getCartForUserOrSession($request);
        $item = $this->repo->updateItem($cart, $itemId, $data);
        $this->logger->info('Cart item updated', ['cart_id' => $cart->id ?? null, 'item_id' => $item->id ?? null]);
        return $item;
    }

    public function removeItem(Request $request, int $itemId)
    {
        $cart = $this->repo->getCartForUserOrSession($request);
        $ok = $this->repo->removeItem($cart, $itemId);
        $this->logger->info('Cart item removed', ['cart_id' => $cart->id ?? null, 'item_id' => $itemId, 'success' => $ok]);
        return $ok;
    }

    public function getCart(Request $request)
    {
        return $this->repo->getCartForUserOrSession($request);
    }

    public function clearCart($cart)
    {
        return $this->repo->clearCart($cart);
    }
}
