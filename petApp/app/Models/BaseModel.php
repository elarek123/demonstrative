<?php

namespace App\Models;

use App\Contracts\CacheRelatedContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class BaseModel extends Model implements CacheRelatedContract
{
    public function cacheDelete(){
        $this->product_geos()->each(function(ProductGeo $productGeo){
            Cache::forget('productgeo:' . $productGeo->id . ':finalprice');
        });
    }
    public function delete()
    {
        $this->cacheDelete();
        return parent::delete();
    }
}
