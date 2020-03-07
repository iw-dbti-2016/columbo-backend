<?php

namespace Columbo\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Columbo\Location;
use Columbo\POI;
use Columbo\Policies\LocationPolicy;
use Columbo\Policies\POIPolicy;
use Columbo\Policies\ReportPolicy;
use Columbo\Policies\SectionPolicy;
use Columbo\Policies\TripPolicy;
use Columbo\Report;
use Columbo\Section;
use Columbo\Trip;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'Columbo\Model' => 'Columbo\Policies\ModelPolicy',
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
