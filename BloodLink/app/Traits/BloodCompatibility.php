<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BloodCompatibility
{
    public static function getCompatibleBloodTypes(string $requestedType): array
    {
        $compatibility = [
            'O+' => ['O+'],
            'O-' => ['O-'],
            'A+' => ['O+', 'A+'],
            'A-' => ['O-', 'A-'],
            'B+' => ['O+', 'B+'],
            'B-' => ['O-', 'B-'],
            'AB+' => ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'],
            'AB-' => ['O-', 'A-', 'B-', 'AB-'],
        ];

        return $compatibility[$requestedType] ?? [];
    }

    public function scopeCompatibleWith(Builder $query, string $bloodType): Builder
    {
        return $query->whereIn('blood_type', self::getCompatibleBloodTypes($bloodType));
    }
}
