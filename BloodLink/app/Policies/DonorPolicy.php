<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Donor;

class DonorPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Donor $donor): bool
    {
        return $user->id === $donor->user_id || $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'donor' || $user->role === 'admin';
    }

    public function update(User $user, Donor $donor): bool
    {
        return $user->id === $donor->user_id || $user->role === 'admin';
    }

    public function delete(User $user, Donor $donor): bool
    {
        return $user->id === $donor->user_id || $user->role === 'admin';
    }
}
