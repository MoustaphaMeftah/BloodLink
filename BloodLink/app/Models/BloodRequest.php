<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
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
