<?php

namespace App\Providers;

use App\Models\District;
use App\Models\Opd;
use App\Policies\DistrictPolicy;
use App\Policies\OpdPolicy;
use App\Policies\PostPolicy;
use App\Policies\PostPolicyOpd;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Opd::class, OpdPolicy::class);
        Gate::policy(District::class, DistrictPolicy::class);
    }

}