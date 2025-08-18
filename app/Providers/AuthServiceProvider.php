<?php

namespace App\Providers;

use App\Models\District;
use App\Models\Opd;
use App\Policies\DistrictPolicy;
use App\Policies\OpdPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Opd::class => OpdPolicy::class,
        District::class => DistrictPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
