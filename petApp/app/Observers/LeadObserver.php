<?php

namespace App\Observers;


use App\Jobs\PriceChangedNotification;
use App\Models\Lead;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LeadObserver
{

    public function check(Lead $lead)
    {
        $productGeo = $lead->product_geo;
        if(!empty($productGeo)){
            if(Cache::get('productgeo:' . $productGeo->id . ':finalprice')){
                Cache::forget('productgeo:' . $productGeo->id . ':finalprice');
            }
            Cache::add('productgeo:' . $productGeo->id . ':finalprice', $productGeo->calculateFinalPrice());

            Log::info("ProductGeo: {$productGeo->id}" . " final price: {$productGeo->calculateFinalPrice()}");
            PriceChangedNotification::dispatch($productGeo);
        }
    }
    public function creating(Lead $lead)
    {
        $this->check($lead);
    }

    public function updating(Lead $lead)
    {
        if($lead->getOriginal('product_geo_id') != $lead->product_geo_id){
            $this->check($lead);
        }
    }
}
