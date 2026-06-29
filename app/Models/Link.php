<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
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

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => \Carbon\Carbon::parse($value)->format('Y-m-d'),
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(Click::class);
    }
}