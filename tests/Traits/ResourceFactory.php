<?php

namespace Tests\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Crypt;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait ResourceFactory
{
	protected function createUser($data=[])
	{
		return factory(User::class)->create($data);
	}

	protected function createTrip(User $user, $data=[])
	{
		return $user->tripsOwner()->save(factory(Trip::class)->make($data));
	}

	protected function createReport(User $user, Trip $trip, $data=[])
	{
		$report = factory(Report::class)->make($data);
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

		return $report;
	}

	protected function createSection(User $user, Report $report, $data=[])
	{
		$section = factory(Section::class)->make($data);
        $section->owner()->associate($user);
        $section->report()->associate($report);
        $section->save();

		return $section;
	}
}
