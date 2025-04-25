<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProductResource extends JsonResource
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
            'price' => $this->price,
            'geos' => GeoResource::collection($this->whenLoaded('geos')),
            'product_geos' => ProductGeoResource::collection($this->whenLoaded('product_geos')),
            'leads' => LeadResource::collection($this->whenLoaded('leads')),

        ];
    }
}
