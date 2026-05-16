<?php

namespace App\View\Composers;

use App\ViewModels\ProductCardViewModel;
use Illuminate\View\View;

class ProductCardComposer
{
    public function compose(View $view): void
    {
        $data = $view->getData();
        $product = $data['product'] ?? null;

        if (! $product) {
            return;
        }

        $context = [
            'brand_slug' => $data['brand_slug'] ?? null,
            'category_slug' => $data['category_slug'] ?? null,
            'subcategory_slug' => $data['subcategory_slug'] ?? null,
            'wishlistVariantIds' => $data['wishlistVariantIds'] ?? [],
        ];

        $view->with(app()->makeWith(ProductCardViewModel::class, [
            'product' => $product,
            'context' => $context,
        ])->toArray());
    }
}
