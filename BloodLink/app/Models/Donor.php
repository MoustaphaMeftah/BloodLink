<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\BloodCompatibility;

class Donor extends Model
{
    use HasFactory, BloodCompatibility;

    protected $fillable = [
        'user_id',
        'blood_type',
        'city',
        'latitude',
        'longitude',
        'availability',
        'contact_verified',
        'last_donation_date',
        'medical_history'
    ];

    protected $casts = [
        'availability' => 'boolean',
        'contact_verified' => 'boolean',
        'last_donation_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(DonorResponse::class);
    }

    public function bloodRequests(): BelongsToMany
    {
        return $this->belongsToMany(BloodRequest::class, 'blood_request_donor')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function isDonationEligible(): bool
    {
        if (!$this->last_donation_date) {
            return true;
        }
        
        return $this->last_donation_date->diffInDays(now()) >= 56;
    }

    public function getDaysUntilEligible(): int
    {
        if (!$this->last_donation_date) {
            return 0;
        }

        $daysSinceLastDonation = $this->last_donation_date->diffInDays(now());
        return max(0, 56 - $daysSinceLastDonation);
    }
}
