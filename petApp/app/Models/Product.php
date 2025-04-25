<?php

namespace App\Models;

use App\Traits\HasLikesTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Product extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;


    protected $fillable = [
        'name',
        'price',
    ];

    public function geos()
    {
        return $this->belongsToMany(Geo::class, 'product_geos', 'product_id', 'geo_id');
    }

    public function leads()
    {
        return $this->hasManyThrough(Lead::class, ProductGeo::class, 'product_id', 'product_geo_id');
    }

    public function product_geos()
    {
        return $this->hasMany(ProductGeo::class);
    }
}
