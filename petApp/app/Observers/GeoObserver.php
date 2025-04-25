<?php

namespace App\Observers;


use App\Jobs\PriceChangedNotification;
use App\Models\Geo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isEmpty;

class GeoObserver
{

    public function check(Geo $geo)
    {
        $productGeos = $geo->product_geos;
        if(!empty($productGeos)){
            foreach ($productGeos as $productGeo){
                if(Cache::get('productgeo:' . $productGeo->id . ':finalprice')){
                    Cache::forget('productgeo:' . $productGeo->id . ':finalprice');
                }
                Cache::add('productgeo:' . $productGeo->id . ':finalprice', $productGeo->calculateFinalPrice());

                Log::info("ProductGeo: {$productGeo->id}" . " final price: {$productGeo->calculateFinalPrice()}");
                PriceChangedNotification::dispatch($productGeo);
            }
        }
    }
    public function creating(Geo $geo)
    {
        $this->check($geo);
    }

    public function updating(Geo $geo)
    {
        Log::info('Зашел shipping_costprev:' . $geo->getOriginal('shipping_cost') . ' shipping_cost:' . $geo->shipping_cost . ' currency_valueprev:' . $geo->getOriginal('currency_value') . ' currency_value:' . $geo->currency_value);
        if($geo->getOriginal('shipping_cost') !=-$geo->shipping_cost || $geo->getOriginal('currency_value') != $geo->currency_value){
            $this->check($geo);
        }
    }
}
