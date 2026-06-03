<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'blood_type',
        'city',
        'availability',
        'last_donation_date',
        'medical_history'
    ];

    protected $casts = [
        'availability' => 'boolean',
        'last_donation_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function responses()
    {
        return $this->hasMany(DonorResponse::class);
    }
}
