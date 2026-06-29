<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Click extends Model
{

    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "link_id",
        "ip_address",
        "country",
        "city",
        "device_type",
        "browser",
        "os",
        "referer",
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
