<?php

namespace App\View\Composers;

use App\DTOs\CartIdentifier;
use App\Helpers\ClientHelper;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ClientComposer
{
    public function compose(View $view)
    {
        $activeBrands = ClientHelper::getActiveBrands();
        $categories = \App\Models\Categories::where('status', 'active')->with(['subcategories' => function($q) {
            $q->where('status', 'active');
        }])->get();

        $view->with([
            'brands' => $activeBrands,
            'categories' => $categories,
            'subCategories' => $categories->flatMap->subcategories,
        ]);

        $wishlistVariantIds = [];
        $wishlistCount = 0;
        if (Auth::check()) {
            $wishlistVariantIds = \App\Models\Wishlist::where('user_id', Auth::id())->pluck('product_variant_id')->toArray();
            $wishlistCount = \App\Models\Wishlist::where('user_id', Auth::id())->count();
        }

        $siteContent = ClientHelper::getSiteContent();
        $view->with('siteContent', $siteContent);

        $view->with('wishlistVariantIds', $wishlistVariantIds);

        $compareCount = 0;
        try {
            $compareService = app(\App\Services\CompareService::class);
            $compareState = $compareService->getCount();
            $compareCount = (int) ($compareState['count'] ?? 0);
        } catch (\Throwable $e) {
            $compareCount = 0;
        }

        $cartItemCount = 0;
        try {
            $cartService = app(\App\Services\CartService::class);
            $cartId = CartIdentifier::fromRequest(request());
            $cart = $cartService->getCart($cartId);
            if ($cart && $cart->relationLoaded('items')) {
                $cartItemCount = (int) $cart->items->sum('quantity');
            } elseif ($cart) {
                $cartItemCount = (int) $cart->items()->sum('quantity');
            }
        } catch (\Throwable $e) {
            $cartItemCount = 0;
        }

        $view->with('cartItemCount', $cartItemCount);
        $view->with('wishlistCount', $wishlistCount);
        $view->with('compareCount', $compareCount);
    }
}
