<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Friend extends Model
{
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requested(): BelongsTo
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public static function areFriends(int $userId, int $otherId): bool
    {
        return static::where(function ($q) use ($userId, $otherId) {
            $q->where('user_id', $userId)->where('friend_id', $otherId)->where('status', 'accepted');
        })->orWhere(function ($q) use ($userId, $otherId) {
            $q->where('user_id', $otherId)->where('friend_id', $userId)->where('status', 'accepted');
        })->exists();
    }
}
