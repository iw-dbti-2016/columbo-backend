<?php

namespace TravelCompanion\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use TravelCompanion\Location;
use TravelCompanion\POI;
use TravelCompanion\Policies\LocationPolicy;
use TravelCompanion\Policies\POIPolicy;
use TravelCompanion\Policies\ReportPolicy;
use TravelCompanion\Policies\SectionPolicy;
use TravelCompanion\Policies\TripPolicy;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'TravelCompanion\Model' => 'TravelCompanion\Policies\ModelPolicy',
        //
        // Auto-discovery should do just fine, but here's the mapping if not:
        // Trip::class => TripPolicy::class,
        // Report::class => ReportPolicy::class,
        // Section::class => SectionPolicy::class,
        // Location::class => LocationPolicy::class,
        // POI::class => POIPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
