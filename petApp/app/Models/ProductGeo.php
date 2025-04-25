<?php

namespace App\Models;

use App\Traits\HasLikesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductGeo extends Pivot
{
    protected $table = 'product_geos';

    use HasLikesTrait;


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function geo()
    {
        return $this->belongsTo(Geo::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'product_geo_id', 'id');
    }
    public function calculateFinalPrice()
    {
        $leadCount = $this->leads()->count();
        Log::info(($this->product->price * $this->geo->currency_value + $this->geo->shipping_cost)/(!empty($leadCount) ? $leadCount : 1));
        return ($this->product->price * $this->geo->currency_value + $this->geo->shipping_cost)/(!empty($leadCount) ? $leadCount : 1);
    }

    public function finalPrice()
    {
        return Cache::get('productgeo:' . $this->id . ':finalprice') ?? $this->calculateFinalPrice();
    }

    public function scopeWithName($query)
    {
        return $query->selectRaw("concat(products.name, ' from ', geos.name) as product_geo_name")->join('products', 'product_geos.product_id', '=', 'products.id')->join('geos', 'product_geos.geo_id', '=', 'geos.id');
    }

    public function delete()
    {
        Cache::forget('productgeo:' . $this->id . ':finalprice');
        parent::delete();
    }
}
