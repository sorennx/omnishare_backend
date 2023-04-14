<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'post_channel_id',
        'post_title',
        'post_short_description',
        'post_content',
        'post_publication_date',
        'post_tags',
        'user_id'
    ];
}
