<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BloodRequest;

class BloodRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BloodRequest $bloodRequest): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'hospital' || $user->role === 'admin';
    }

    public function update(User $user, BloodRequest $bloodRequest): bool
    {
        return $user->hospital?->id === $bloodRequest->hospital_id || $user->role === 'admin';
    }

    public function delete(User $user, BloodRequest $bloodRequest): bool
    {
        return $user->hospital?->id === $bloodRequest->hospital_id || $user->role === 'admin';
    }
}
