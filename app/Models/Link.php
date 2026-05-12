<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
       "user_id",
       "original_url",
       "slug",
       "title",
       "expires_at",
       "password",
       "click_limit"
    ];  

    protected $attributes = [
        "is_active" => true,
        "clicks_count" => 0,
    ];
}