<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;

class Ga4Ecommerce
{
    public const CURRENCY = 'MAD';

    public static function item(?ProductVariant $variant, ?Product $product, int $quantity = 1, mixed $price = null): ?array
    {
        try {
            return self::buildItem($variant, $product, $quantity, $price);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function cartPayload(?Cart $cart, array $totals = []): ?array
    {
        try {
            return self::buildCartPayload($cart, $totals);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function purchasePayload(?Order $order): ?array
    {
        try {
            return self::buildPurchasePayload($order);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function buildItem(?ProductVariant $variant, ?Product $product, int $quantity, mixed $price): ?array
    {
        if (!$variant && !$product) {
            return null;
        }

        if ($variant && !$product) {
            $product = $variant->product;
        }

        $sku = $variant ? self::plain($variant->sku) : '';
        if ($sku === '' && $product) {
            $sku = self::plain($product->sku);
        }
        if ($sku === '' && $variant) {
            $sku = 'variant-' . $variant->id;
        }
        if ($sku === '' && $product) {
            $sku = 'product-' . $product->id;
        }
        if ($sku === '') {
            return null;
        }

        $productName = $product ? self::translated($product, 'name') : '';
        $variantLabel = $variant ? self::translated($variant, 'variant_name') : '';
        $name = $productName;
        if ($variantLabel !== '' && strcasecmp($variantLabel, $productName) !== 0) {
            $name = trim($productName . ($productName !== '' ? ' - ' : '') . $variantLabel);
        }
        if ($name === '') {
            $name = 'Product';
        }

        $brand = $product ? self::translated($product->brand, 'name') : '';
        $category = $product ? self::translated($product->category, 'category-name') : '';

        $unitPrice = $price;
        if ($unitPrice === null) {
            $unitPrice = $variant?->price ?? $product?->price ?? 0;
        }

        return [
            'item_id' => $sku,
            'item_name' => $name,
            'item_brand' => $brand,
            'item_category' => $category,
            'price' => self::amount($unitPrice),
            'quantity' => max(1, $quantity),
        ];
    }

    private static function buildCartPayload(?Cart $cart, array $totals): ?array
    {
        if (!$cart) {
            return null;
        }

        $items = self::itemsFromCart($cart);
        if ($items === []) {
            return null;
        }

        return [
            'currency' => self::CURRENCY,
            'value' => self::amount($totals['final_total'] ?? 0),
            'items' => $items,
        ];
    }

    private static function buildPurchasePayload(?Order $order): ?array
    {
        if (!$order || !$order->order_number) {
            return null;
        }

        $order->loadMissing(['items.variant', 'items.product.brand', 'items.product.category']);

        $items = [];
        foreach ($order->items as $item) {
            $built = self::itemFromOrderItem($item);
            if ($built) {
                $items[] = $built;
            }
        }

        if ($items === []) {
            return null;
        }

        return [
            'transaction_id' => (string) $order->order_number,
            'value' => self::amount($order->total),
            'currency' => self::CURRENCY,
            'shipping' => self::amount($order->shipping_cost ?? 0),
            'items' => $items,
        ];
    }

    public static function itemsFromCart(Cart $cart): array
    {
        $cart->loadMissing(['items.variant', 'items.product.brand', 'items.product.category']);

        $items = [];
        foreach ($cart->items as $item) {
            $built = self::itemFromCartItem($item);
            if ($built) {
                $items[] = $built;
            }
        }

        return $items;
    }

    private static function itemFromCartItem(CartItem $item): ?array
    {
        $price = $item->final_price !== null && $item->final_price !== ''
            ? $item->final_price
            : $item->unit_price;

        return self::item($item->variant, $item->product, (int) $item->quantity, $price);
    }

    private static function itemFromOrderItem(OrderItem $item): ?array
    {
        return self::item($item->variant, $item->product, (int) $item->quantity, $item->unit_price);
    }

    private static function translated(mixed $model, string $field): string
    {
        if (!$model) {
            return '';
        }

        if (is_object($model) && method_exists($model, 'getTranslation')) {
            $locale = app()->getLocale() ?: 'en';
            $value = $model->getTranslation($field, $locale, false);
            if (!is_string($value) || trim($value) === '') {
                $value = $model->getTranslation($field, 'en', false);
            }

            return self::plain($value);
        }

        $raw = is_object($model) ? ($model->{$field} ?? '') : $model;

        return self::plain($raw);
    }

    private static function plain(mixed $value): string
    {
        if (is_array($value)) {
            $locale = app()->getLocale() ?: 'en';
            $value = $value[$locale] ?? ($value['en'] ?? reset($value) ?: '');
        }

        $text = trim(strip_tags(html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8')));

        return preg_replace('/\s+/', ' ', $text) ?? $text;
    }

    private static function amount(mixed $value): float
    {
        return round((float) $value, 2);
    }
}
