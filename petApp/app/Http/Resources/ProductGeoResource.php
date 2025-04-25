<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductGeoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::guard('sanctum')->setRequest($request)->user();
        log::info($user);
        return [
            'id' => $this->id,
            'final_price' => $this->finalPrice(),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'geo' => GeoResource::make($this->whenLoaded('geo')),
            'lead' => LeadResource::make($this->whenLoaded('lead')),
            'is_liked' => $this->is_liked
        ];
    }
}
