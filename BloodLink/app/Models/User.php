<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use \Illuminate\Auth\MustVerifyEmail, HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'city',
        'email_verified_at',
        'verification_code',
        'password_reset_token',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'password_reset_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login' => 'datetime',
    ];

    public function donor(): HasOne
    {
        return $this->hasOne(Donor::class);
    }

    public function hospital(): HasOne
    {
        return $this->hasOne(Hospital::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function sentFriendRequests(): HasMany
    {
        return $this->hasMany(Friend::class, 'user_id');
    }

    public function receivedFriendRequests(): HasMany
    {
        return $this->hasMany(Friend::class, 'friend_id');
    }

    public function acceptedFriends()
    {
        $sent = $this->sentFriendRequests()->where('status', 'accepted')->with('requested');
        $received = $this->receivedFriendRequests()->where('status', 'accepted')->with('requester');
        return $sent->get()->map(fn($f) => $f->requested)
            ->merge($received->get()->map(fn($f) => $f->requester))
            ->unique('id')
            ->values();
    }

    public function isFriendWith(User $other): bool
    {
        return Friend::areFriends($this->id, $other->id);
    }

    public function hasPendingRequestFrom(User $other): bool
    {
        return Friend::where('user_id', $other->id)
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function hasSentRequestTo(User $other): bool
    {
        return Friend::where('user_id', $this->id)
            ->where('friend_id', $other->id)
            ->where('status', 'pending')
            ->exists();
    }
}
