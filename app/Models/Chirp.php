<?php

namespace App\Models;

use Database\Factories\ChirpFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chirp extends Model
{
    /** @use HasFactory<ChirpFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['message'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->oldest();
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
