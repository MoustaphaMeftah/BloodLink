<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'blood_type',
        'quantity',
        'urgency',
        'location',
        'status'
    ];

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(DonorResponse::class);
    }

    public function donors(): BelongsToMany
    {
        return $this->belongsToMany(Donor::class, 'blood_request_donor')
            ->withPivot('status')
            ->withTimestamps();
    }
}
