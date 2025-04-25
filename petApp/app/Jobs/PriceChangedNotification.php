<?php

namespace App\Jobs;

use App\Http\Resources\UserResource;
use App\Models\ProductGeo;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceChangedNotification implements ShouldQueue
{
    use Dispatchable, Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly ProductGeo $productGeo)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("НУ И ХУЙНЯ");
        try {
            //Log::info(implode(',',  $this->productGeo->getLikedUsers()->get()->toArray()));
            Redis::publish(env('REDIS_PUSH_CHANNEL', 'telegram:notifications'), json_encode([
                'telegram_ids' => array_values(array_filter(
                    $this->productGeo->getLikedUsers()->pluck('telegram_id')->toArray()
                )),
                'product_name' => $this->productGeo->product->name,
                'geo_name' => $this->productGeo->geo->name,
                'price' => $this->productGeo->finalPrice(),
            ]));
        } catch (Exception $exception) {
            Log::error("Не отправлено: " . $exception->getMessage());
        }
    }
}
