<?php

namespace App\Repositories\Client\Interfaces;

interface WishlistRepositoryInterface
{
    public function index();
    public function addToWishlist($userId, $productVariantId);
    public function removeFromWishlist($userId, $productVariantId);
    public function checkWishlist($userId, $productVariantId);
    public function countWishlist($userId);
    public function getWishlist($userId);
    public function clearWishlist($userId);
}
