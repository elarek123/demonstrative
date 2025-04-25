<?php

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;

trait HasLikesTrait
{
    /**
     * @return MorphMany
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'liked');
    }

    /**
     * @return Model
     */
    public function like(): Model
    {
        if (!$this->likes()->where('user_id', auth()->id())->exists()) {
            Log::info($this);
            return $this->likes()->create(['user_id' => auth()->id()]);
        }

        return $this->likes()->where('user_id', auth()->id())->first();
    }

    /**
     * @return void
     */
    public function unlike(): void
    {
        $this->likes()->where('user_id', auth()->id())->get()->each->delete();
    }

    /**
     * @return bool
     */
    public function getIsLikedAttribute()
    {
        return (bool) $this->likes()->where('user_id', auth()->id())->count();
    }

    public function getLikedUsers()
    {
       return $this->hasManyThrough(User::class, Like::class, 'liked_id', 'id', 'id', 'user_id');
    }
}
