<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BloodCompatibility
{
    public static function getCompatibleBloodTypes(string $requestedType): array
    {
        $donorsForType = [
            'O+' => ['O+', 'O-'],
            'O-' => ['O-'],
            'A+' => ['A+', 'A-', 'O+', 'O-'],
            'A-' => ['A-', 'O-'],
            'B+' => ['B+', 'B-', 'O+', 'O-'],
            'B-' => ['B-', 'O-'],
            'AB+' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
        ];

        return $donorsForType[$requestedType] ?? [];
    }

    public static function getDonatableBloodTypes(string $donorBloodType): array
    {
        $recipientsForDonor = [
            'O+' => ['O+', 'A+', 'B+', 'AB+'],
            'O-' => ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'],
            'A+' => ['A+', 'AB+'],
            'A-' => ['A+', 'A-', 'AB+', 'AB-'],
            'B+' => ['B+', 'AB+'],
            'B-' => ['B+', 'B-', 'AB+', 'AB-'],
            'AB+' => ['AB+'],
            'AB-' => ['AB+', 'AB-'],
        ];

        return $recipientsForDonor[$donorBloodType] ?? [];
    }

    public function scopeCompatibleWith(Builder $query, string $bloodType): Builder
    {
        return $query->whereIn('blood_type', self::getCompatibleBloodTypes($bloodType));
    }

    public function scopeDonatableTo(Builder $query, string $donorBloodType): Builder
    {
        return $query->whereIn('blood_type', self::getDonatableBloodTypes($donorBloodType));
    }
}
