<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->getTranslation('name', 'en'),
            'description' => $this->getTranslation('description', 'en'),
            'is_bestseller' => (bool) $this->is_bestseller,
            'brand' => $this->brand ? [
                'id' => $this->brand->id,
                'name' => $this->brand->getTranslation('name', 'en'),
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->getTranslation('category-name', 'en'),
            ] : null,
            'images' => $this->images->map(function ($img) {
                $path = $img->path ?? $img->image ?? null;
                return $path ? asset('storage/' . ltrim($path, '/')) : null;
            })->filter()->values(),
            'variants' => ProductVariantResource::collection($this->whenLoaded('productVariants')),
            'price_min' => $this->min_price ?? null,
            'price_max' => $this->max_price ?? null,
        ];
    }
}
