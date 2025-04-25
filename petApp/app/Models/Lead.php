<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Lead extends Model
{
    /** @use HasFactory<\Database\Factories\LeadFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'product_geo_id',
    ];

    public function product_geo()
    {
        return $this->belongsTo(ProductGeo::class);
    }
    public function products()
    {
        return $this->hasManyThrough(Product::class, ProductGeo::class);
    }

    public function geos()
    {
        return $this->hasManyThrough( Geo::class, ProductGeo::class);
    }

    public function delete()
    {
        $productGeo = $this->product_geo;
        $response = parent::delete();
        Cache::put('productgeo:' . $productGeo->id . ':finalprice', $productGeo->calculateFinalPrice());
        return $response;
    }
}
