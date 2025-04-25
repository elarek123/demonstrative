<?php

namespace App\Models;

use App\Traits\HasLikesTrait;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasLikesTrait;

    protected $table = 'likes';

    protected $fillable = [
        'liked_id',
        'liked_type',
        'user_id',
    ];

}
