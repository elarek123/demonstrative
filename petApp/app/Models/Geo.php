<?php

namespace App\Models;

use Illuminate\Cache\Events\CacheHit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Geo extends BaseModel
{
    /** @use HasFactory<\Database\Factories\GeoFactory> */
    use HasFactory;

    protected $table = 'geos';

    protected $fillable = [
        'name',
        'shipping_cost',
        'currency_name',
        'currency_value'
    ];



    public function leads()
    {
        return $this->hasManyThrough(Lead::class, ProductGeo::class, 'geo_id', 'product_geo_id');
    }

    public function products(){
        return $this->belongsToMany(Product::class, 'product_geos', 'geo_id', 'product_id');
    }

    public function product_geos(){
        return $this->hasMany(ProductGeo::class);
    }
}
