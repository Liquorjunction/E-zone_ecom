<?php

namespace App\View\Composers;

use App\Helpers\ClientHelper;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ClientComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Fetch active brands and categories (with subcategories) and pass to all client views
        $activeBrands = ClientHelper::getActiveBrands();
        $categories = \App\Models\Categories::where('status', 'active')->with(['subcategories' => function($q) {
            $q->where('status', 'active');
        }])->get();

        $view->with([
            'brands' => $activeBrands,
            'categories' => $categories,
            'subCategories' => $categories->flatMap->subcategories,
        ]);

        // Provide wishlist variant ids for the authenticated user to avoid per-item queries
        $wishlistVariantIds = [];
        if (Auth::check()) {
            $wishlistVariantIds = \App\Models\Wishlist::where('user_id', Auth::id())->pluck('product_variant_id')->toArray();
        }

        $view->with('wishlistVariantIds', $wishlistVariantIds);
            // Provide cart item count (sum of quantities) for current session/user
            $cartItemCount = 0;
            try {
                $cartService = app(\App\Services\CartService::class);
                $cart = $cartService->getCart(request());
                if ($cart && $cart->relationLoaded('items')) {
                    $cartItemCount = (int) $cart->items->sum('quantity');
                } elseif ($cart) {
                    $cartItemCount = (int) $cart->items()->sum('quantity');
                }
            } catch (\Throwable $e) {
                $cartItemCount = 0;
            }

            $view->with('cartItemCount', $cartItemCount);
    }
}
