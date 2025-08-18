<?php

namespace App\Providers;

use App\Models\District;
use App\Models\Opd;
use App\Models\Report;
use App\Models\ReportAttachment;
use App\Models\ReportCategory;
use App\Models\ReportStatusHistory;
use App\Models\ReportType;
use App\Models\Village;
use App\Policies\DistrictPolicy;
use App\Policies\OpdPolicy;
use App\Policies\ReportAttachmentPolicy;
use App\Policies\ReportCategoryPolicy;
use App\Policies\ReportPolicy;
use App\Policies\ReportStatusHistoryPolicy;
use App\Policies\ReportTypePolicy;
use App\Policies\VillagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Opd::class => OpdPolicy::class,
        District::class => DistrictPolicy::class,
        ReportAttachment::class => ReportAttachmentPolicy::class,
        ReportCategory::class => ReportCategoryPolicy::class,
        Report::class => ReportPolicy::class,
        ReportStatusHistory::class => ReportStatusHistoryPolicy::class,
        ReportType::class => ReportTypePolicy::class,
        Village::class => VillagePolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
