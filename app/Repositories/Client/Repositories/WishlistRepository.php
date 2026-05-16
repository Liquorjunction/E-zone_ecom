<?php

namespace App\Repositories\Client\Repositories;
use App\Models\Wishlist;
use App\Repositories\Client\Interfaces\WishlistRepositoryInterface;

class WishlistRepository implements WishlistRepositoryInterface
{
    public function index()
    {
        return Wishlist::all();
    }

    public function addToWishlist($userId, $productVariantId)
    {
        return Wishlist::create([
            'user_id' => $userId,
            'product_variant_id' => $productVariantId,
        ]);
    }

    public function removeFromWishlist($userId, $productVariantId)
    {
        return Wishlist::where('user_id', $userId)
            ->where('product_variant_id', $productVariantId)
            ->delete();
    }

    public function checkWishlist($userId, $productVariantId)
    {
        return Wishlist::where('user_id', $userId)
            ->where('product_variant_id', $productVariantId)
            ->exists();
    }

    public function countWishlist($userId)
    {
        return Wishlist::where('user_id', $userId)->count();
    }

    public function getWishlist($userId)
    {
        return Wishlist::where('user_id', $userId)->get();
    }

    public function clearWishlist($userId)
    {
        return Wishlist::where('user_id', $userId)->delete();
    }
}
