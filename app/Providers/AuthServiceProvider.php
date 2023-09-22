<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\AssignedConfirmer;
use App\Models\AssignedEmployee;
use App\Models\AssignedExhibitor;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\UserGroup;
use App\Models\Venue;
use App\Policies\AssignedConfirmerPolicy;
use App\Policies\AssignedEmployeePolicy;
use App\Policies\AssignedExhibitorPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\ExhibitPolicy;
use App\Policies\LeadPolicy;
use App\Policies\RoiPolicy;
use App\Policies\SourcePolicy;
use App\Policies\StatusPolicy;
use App\Policies\SurveyPolicy;
use App\Policies\UserGroupPolicy;
use App\Policies\VenuePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        AssignedConfirmer::class => AssignedConfirmerPolicy::class,
        AssignedEmployee::class => AssignedEmployeePolicy::class,
        AssignedExhibitor::class => AssignedExhibitorPolicy::class,
        Employee::class => EmployeePolicy::class,
        Exhibit::class => ExhibitPolicy::class,
        Lead::class => LeadPolicy::class,
        Roi::class => RoiPolicy::class,
        Source::class => SourcePolicy::class,
        Status::class => StatusPolicy::class,
        Survey::class => SurveyPolicy::class,
        UserGroup::class => UserGroupPolicy::class,
        Venue::class => VenuePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
