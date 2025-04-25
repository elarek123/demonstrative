<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GeoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shipping_cost' => $this->shipping_cost,
            'currency_name' => $this->currency_name,
            'currency_value' => $this->currency_value,
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'leads' => LeadResource::collection($this->whenLoaded('leads')),
            'product_geos' => ProductGeoResource::collection($this->whenLoaded('product_geos')),
        ];
    }
}
