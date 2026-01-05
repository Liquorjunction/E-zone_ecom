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

        // Resolve product and variant (include soft-deleted) to ensure we always have a price source
        $productId = $payload['product_id'] ?? null;
        $variantId = $payload['variant_id'] ?? null;

        $product = $productId ? \App\Models\Product::with(['productVariants' => function ($q) {
            $q->withTrashed();
        }])->withTrashed()->find($productId) : null;

        $variant = $variantId && $product
            ? $product->productVariants->firstWhere('id', $variantId)
            : null;

        // If variant not provided, pick the first available (even soft-deleted) for pricing consistency
        if (!$variant && $product && $product->productVariants->count() > 0) {
            $variant = $product->productVariants->first();
            $payload['variant_id'] = $variant->id;
        }

        // Compute unit price: prefer variant price, then provided unit_price, then product price, fallback to 0
        $unitPrice = $payload['unit_price'] ?? null;
        if ($unitPrice === null) {
            $unitPrice = $variant->price ?? $variant->sale_price ?? $product->price ?? 0;
        }

        $payload['unit_price'] = $unitPrice;

        $item = $this->repo->addItem($cart, $payload);
        $this->logger->info('Cart item added', [
            'cart_id' => $cart->id ?? null,
            'item_id' => $item->id,
            'variant_id' => $payload['variant_id'] ?? null,
            'unit_price' => $unitPrice,
        ]);
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
