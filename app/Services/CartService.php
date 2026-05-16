<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\Client\Interfaces\CartRepositoryInterface;
use App\Repositories\Client\Interfaces\ProductRepositoryInterface;
use App\DTOs\CartIdentifier;
use Psr\Log\LoggerInterface;

class CartService
{
    public const SESSION_KEY_ACTIVE_BUY_NOW_CART_ID = 'active_buy_now_cart_id';
    public const SESSION_KEY_BUY_NOW_CONTEXT = 'buy_now_context';

    protected $repo;
    protected $productRepo;
    protected $logger;
    protected SessionCouponService $sessionCoupon;

    public function __construct(
        CartRepositoryInterface $repo,
        ProductRepositoryInterface $productRepo,
        LoggerInterface $logger,
        SessionCouponService $sessionCoupon
    )
    {
        $this->repo = $repo;
        $this->productRepo = $productRepo;
        $this->logger = $logger;
        $this->sessionCoupon = $sessionCoupon;
    }

    public function addItem(CartIdentifier $cartId, array $payload)
    {
        $cart = $this->repo->getCartForUserOrSession($cartId);
        return $this->addItemToCart($cart, $payload);
    }

    public function addItemToCart(Cart $cart, array $payload)
    {
        $payloadData = $this->preparePayloadAndValidateStock($cart, $payload);

        $item = $this->repo->addItem($cart, $payloadData);
        $this->logger->info('Cart item added', [
            'cart_id' => $cart->id ?? null,
            'item_id' => $item->id,
            'variant_id' => $payloadData['variant_id'] ?? null,
            'unit_price' => $payloadData['unit_price'] ?? null,
        ]);

        return $item;
    }

    private function preparePayloadAndValidateStock(Cart $cart, array $payload): array
    {

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

        $quantity = (int) ($payload['quantity'] ?? 1);
        $existingQty = 0;
        if (!empty($payload['product_id'])) {
            $existingItem = $cart->items->first(function ($item) use ($payload) {
                return (int) $item->product_id === (int) ($payload['product_id'] ?? 0)
                    && (int) ($item->variant_id ?? 0) === (int) ($payload['variant_id'] ?? 0);
            });
            $existingQty = (int) ($existingItem->quantity ?? 0);
        }

        $requiredQty = $quantity + $existingQty;
        $stock = $variant
            ? $variant->getSellableStock()
            : ($product ? $product->productVariants->sum(fn($pv) => method_exists($pv, 'getSellableStock') ? $pv->getSellableStock() : 0) : 0);
        if ($stock < $requiredQty) {
            throw new \Exception(statictranslations('Insufficient stock for this item.'));
        }

        return $payload;
    }

    public function createIsolatedBuyNowCart(CartIdentifier $cartId): Cart
    {
        $cart = Cart::create([
            'user_id' => $cartId->userId,
            'session_id' => $this->buildBuyNowSessionId($cartId->sessionId),
        ]);

        session()->put(self::SESSION_KEY_ACTIVE_BUY_NOW_CART_ID, $cart->id);

        return $cart;
    }

    public function startBuyNowFlow(CartIdentifier $cartId, array $payload): array
    {
        $previousCart = $this->repo->getCartForUserOrSession($cartId);
        $buyNowCart = $this->createIsolatedBuyNowCart($cartId);

        try {
            $item = $this->addItemToCart($buyNowCart, $payload);
        } catch (\Throwable $e) {
            $this->clearCart($buyNowCart);
            $this->clearBuyNowFlowState();
            throw $e;
        }

        session()->put(self::SESSION_KEY_BUY_NOW_CONTEXT, [
            'previous_cart_id' => $previousCart->id ?? null,
            'buy_now_cart_id' => $buyNowCart->id,
            'payload' => [
                'product_id' => $payload['product_id'] ?? null,
                'variant_id' => $payload['variant_id'] ?? null,
                'quantity' => (int) ($payload['quantity'] ?? 1),
            ],
        ]);

        return [
            'cart' => $buyNowCart,
            'item' => $item,
        ];
    }

    public function getActiveBuyNowCart(): ?Cart
    {
        $cartId = (int) session()->get(self::SESSION_KEY_ACTIVE_BUY_NOW_CART_ID);
        if (!$cartId) {
            return null;
        }

        return $this->repo->getCartById($cartId);
    }

    public function getBuyNowContext(): ?array
    {
        $context = session()->get(self::SESSION_KEY_BUY_NOW_CONTEXT);
        return is_array($context) ? $context : null;
    }

    public function recoverIncompleteBuyNowToPreviousCart(CartIdentifier $cartId): bool
    {
        $context = $this->getBuyNowContext();
        if (!$context) {
            return false;
        }

        $buyNowCartId = (int) ($context['buy_now_cart_id'] ?? 0);
        $buyNowCart = $buyNowCartId > 0
            ? $this->repo->getCartById($buyNowCartId)
            : $this->getActiveBuyNowCart();

        $previousCartId = (int) ($context['previous_cart_id'] ?? 0);
        $previousCart = $previousCartId > 0 ? $this->repo->getCartById($previousCartId) : null;

        if ($previousCart && !$this->isCartOwnedByIdentifier($previousCart, $cartId)) {
            $previousCart = null;
        }

        $targetCart = $previousCart ?: $this->repo->getCartForUserOrSession($cartId);

        try {
            $restored = false;

            if ($buyNowCart && $buyNowCart->items && $buyNowCart->items->isNotEmpty()) {
                foreach ($buyNowCart->items as $buyNowItem) {
                    $this->addItemToCart($targetCart, [
                        'product_id' => $buyNowItem->product_id,
                        'variant_id' => $buyNowItem->variant_id,
                        'quantity' => (int) $buyNowItem->quantity,
                    ]);
                }

                $restored = true;
            } elseif (!empty($context['payload']['product_id'])) {
                $this->addItemToCart($targetCart, [
                    'product_id' => $context['payload']['product_id'],
                    'variant_id' => $context['payload']['variant_id'] ?? null,
                    'quantity' => (int) ($context['payload']['quantity'] ?? 1),
                ]);

                $restored = true;
            }

            if ($buyNowCart) {
                $this->clearCart($buyNowCart);
            }

            $this->clearBuyNowFlowState();
            $this->sessionCoupon->remove();

            return $restored;
        } catch (\Throwable $e) {
            $this->logger->warning('Unable to recover incomplete buy-now cart.', [
                'error' => $e->getMessage(),
                'buy_now_cart_id' => $buyNowCartId,
                'previous_cart_id' => $previousCartId,
            ]);

            return false;
        }
    }

    private function isCartOwnedByIdentifier(Cart $cart, CartIdentifier $identifier): bool
    {
        if ($identifier->userId) {
            return (int) $cart->user_id === (int) $identifier->userId;
        }

        return (string) $cart->session_id === (string) $identifier->sessionId;
    }

    public function clearActiveBuyNowCart(): void
    {
        session()->forget(self::SESSION_KEY_ACTIVE_BUY_NOW_CART_ID);
    }

    public function clearBuyNowFlowState(): void
    {
        session()->forget(self::SESSION_KEY_ACTIVE_BUY_NOW_CART_ID);
        session()->forget(self::SESSION_KEY_BUY_NOW_CONTEXT);
    }

    private function buildBuyNowSessionId(string $sessionId): string
    {
        return $sessionId . ':buy-now';
    }

    public function updateItem(CartIdentifier $cartId, int $itemId, array $data)
    {
        $cart = $this->repo->getCartForUserOrSession($cartId);
        $item = $cart->items->firstWhere('id', $itemId);
        if ($item) {
            $variant = $item->variant ?? null;
            $product = $item->product ?? null;
            $quantity = $data['quantity'] ?? $item->quantity;
            $stock = $variant
                ? $variant->getSellableStock()
                : ($product ? $product->productVariants->sum(fn($pv) => method_exists($pv, 'getSellableStock') ? $pv->getSellableStock() : 0) : 0);
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

        if ($ok && $cart) {
            $freshCart = $this->repo->getCartById((int) $cart->id);

            if (! $freshCart || $freshCart->items->isEmpty()) {
                $this->sessionCoupon->remove();
                $this->clearBuyNowSessionStateIfReferencesCart((int) $cart->id);

                // Remove empty guest/session cart rows so stale session carts cannot reappear.
                if ($freshCart && !$freshCart->user_id) {
                    $this->repo->clearCart($freshCart);
                }
            }
        }

        $this->logger->info('Cart item removed', ['cart_id' => $cart->id ?? null, 'item_id' => $itemId, 'success' => $ok]);
        return $ok;
    }

    private function clearBuyNowSessionStateIfReferencesCart(int $cartId): void
    {
        if ($cartId <= 0) {
            return;
        }

        $activeBuyNowCartId = (int) session()->get(self::SESSION_KEY_ACTIVE_BUY_NOW_CART_ID, 0);
        $context = $this->getBuyNowContext();
        $contextBuyNowCartId = (int) ($context['buy_now_cart_id'] ?? 0);
        $contextPreviousCartId = (int) ($context['previous_cart_id'] ?? 0);

        if (
            $activeBuyNowCartId === $cartId
            || $contextBuyNowCartId === $cartId
            || $contextPreviousCartId === $cartId
        ) {
            $this->clearBuyNowFlowState();
            session()->forget('checkout_mode');
        }
    }

    public function getCart(CartIdentifier $cartId)
    {
        return $this->repo->getCartForUserOrSession($cartId);
    }

    public function getCartById(int $cartId)
    {
        return $this->repo->getCartById($cartId);
    }

    public function clearCart($cart)
    {
        $result = $this->repo->clearCart($cart);
        $this->sessionCoupon->remove();
        return $result;
    }

    public function clearCartItems($cart)
    {
        $result = $this->repo->clearCartItems($cart);
        $this->sessionCoupon->remove();
        return $result;
    }

    public function mergeSessionCartIntoUserCart(int $userId, string $sessionId): bool
    {
        if ($userId <= 0 || trim($sessionId) === '') {
            return false;
        }

        $sessionCart = $this->repo->getCartBySessionId($sessionId);
        if (! $sessionCart || $sessionCart->items->isEmpty()) {
            return false;
        }

        $userCart = $this->repo->getCartForUserOrSession(new CartIdentifier($userId, $sessionId));

        if (! $userCart || $sessionCart->id === $userCart->id) {
            return false;
        }

        $mergedAny = false;

        foreach ($sessionCart->items as $sessionItem) {
            try {
                $this->addItemToCart($userCart, [
                    'product_id' => $sessionItem->product_id,
                    'variant_id' => $sessionItem->variant_id,
                    'quantity' => (int) $sessionItem->quantity,
                    'unit_price' => $sessionItem->unit_price,
                ]);

                $mergedAny = true;
            } catch (\Throwable $e) {
                $this->logger->warning('Skipping session cart item during merge.', [
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'cart_item_id' => $sessionItem->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->clearCart($sessionCart);

        return $mergedAny;
    }
}
