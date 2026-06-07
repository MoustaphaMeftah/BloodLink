<?php

namespace App\Providers;

use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\Message;
use App\Policies\BloodRequestPolicy;
use App\Policies\DonorPolicy;
use App\Policies\MessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Donor::class => DonorPolicy::class,
        BloodRequest::class => BloodRequestPolicy::class,
        Message::class => MessagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
