<?php

namespace App\Observers;


use App\Jobs\PriceChangedNotification;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProductObserver
{

    public function check(Product $product): Void
    {
        $productGeos = $product->product_geos;
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
    public function creating(Product $product)
    {
        $this->check($product);
    }

    public function updating(Product $product)
    {
        $this->check($product);
    }

}
