<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray($request)
    {
        $inventory = $this->whenLoaded('inventory') ?? $this->inventory ?? null;

        return [
            'id' => $this->id,
            'sku' => $this->sku ?? null,
            'name' => $this->getTranslation('name', 'en') ?? ($this->variant_name ?? null),
            'price' => $this->price ?? null,
            'inventory' => [
                'quantity' => $inventory ? ($inventory->quantity ?? 0) : 0,
                'in_stock' => $inventory ? (($inventory->quantity ?? 0) > 0) : false,
            ],
            'attributes' => $this->attributes ?? [],
        ];
    }
}
