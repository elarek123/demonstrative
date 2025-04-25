<?php

namespace App\Jobs;

use App\Models\ProductGeo;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FinalPrice implements ShouldQueue
{
    use Queueable;


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $productGeos = ProductGeo::all();

            foreach ($productGeos as $productGeo) {
                if(Cache::get('productgeo:' . $productGeo->id . ':finalprice')){
                    Cache::forget('productgeo:' . $productGeo->id . ':finalprice');
                }
                Cache::add('productgeo:' . $productGeo->id . ':finalprice', $productGeo->calculateFinalPrice());

                Log::info("ProductGeo: {$productGeo->id}" . " final price: {$productGeo->calculateFinalPrice()}");
            }
        }
        catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
