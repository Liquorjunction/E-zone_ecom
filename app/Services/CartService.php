<?php

namespace App\Services;

use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use App\DTOs\CartIdentifier;
use Psr\Log\LoggerInterface;

class CartService
{
    protected $repo;
    protected $productRepo;
    protected $logger;

    public function __construct(CartRepositoryInterface $repo, ProductRepositoryInterface $productRepo, LoggerInterface $logger)
    {
        $this->repo = $repo;
        $this->productRepo = $productRepo;
        $this->logger = $logger;
    }

    public function addItem(CartIdentifier $cartId, array $payload)
    {
        $cart = $this->repo->getCartForUserOrSession($cartId);

        $productId = $payload['product_id'] ?? null;
        $variantId = $payload['variant_id'] ?? null;

        $product = $productId ? $this->productRepo->getProductWithVariants($productId) : null;

        $variant = $variantId && $product
            ? $product->productVariants->firstWhere('id', $variantId)
            : null;

        if (!$variant && $product && $product->productVariants->count() > 0) {
            $variant = $product->productVariants->first();
            $payload['variant_id'] = $variant->id;
        }

        $unitPrice = $payload['unit_price'] ?? null;
        if ($unitPrice === null) {
            $unitPrice = $variant->price ?? $variant->sale_price ?? $product->price ?? 0;
        }

        $payload['unit_price'] = $unitPrice;

        $quantity = $payload['quantity'] ?? 1;
        $stock = $variant ? $variant->getTotalStockAttribute() : ($product ? $product->productVariants()->sum('inventory.quantity') : 0);
        if ($stock < $quantity) {
            throw new \Exception(statictranslations('Insufficient stock for this item.'));
        }

        $item = $this->repo->addItem($cart, $payload);
        $this->logger->info('Cart item added', [
            'cart_id' => $cart->id ?? null,
            'item_id' => $item->id,
            'variant_id' => $payload['variant_id'] ?? null,
            'unit_price' => $unitPrice,
        ]);
        return $item;
    }

    public function updateItem(CartIdentifier $cartId, int $itemId, array $data)
    {
        $cart = $this->repo->getCartForUserOrSession($cartId);
        $item = $cart->items->firstWhere('id', $itemId);
        if ($item) {
            $variant = $item->variant ?? null;
            $product = $item->product ?? null;
            $quantity = $data['quantity'] ?? $item->quantity;
            $stock = $variant ? $variant->getSellableStock() : ($product ? $product->productVariants()->sum('inventory.quantity') : 0);
            if ($stock < $quantity) {
                throw new \Exception(statictranslations('Insufficient stock for this item.'));
            }
        }
        $item = $this->repo->updateItem($cart, $itemId, $data);
        $this->logger->info('Cart item updated', ['cart_id' => $cart->id ?? null, 'item_id' => $item->id ?? null]);
        return $item;
    }

    public function removeItem(CartIdentifier $cartId, int $itemId)
    {
        $cart = $this->repo->getCartForUserOrSession($cartId);
        $ok = $this->repo->removeItem($cart, $itemId);
        $this->logger->info('Cart item removed', ['cart_id' => $cart->id ?? null, 'item_id' => $itemId, 'success' => $ok]);
        return $ok;
    }

    public function getCart(CartIdentifier $cartId)
    {
        return $this->repo->getCartForUserOrSession($cartId);
    }

    public function clearCart($cart)
    {
        return $this->repo->clearCart($cart);
    }
}
