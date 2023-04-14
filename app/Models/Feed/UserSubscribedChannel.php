<?php

namespace App\Models\Feed;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscribedChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_channel_id',
        'user_id'
    ];
}