<?php

namespace Tests\Traits;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Columbo\Location;
use Columbo\Report;
use Columbo\Role;
use Columbo\Section;
use Columbo\Trip;
use Columbo\User;

trait ResourceFactory
{
	/**
	 * Creates a user. If data is passed, it is used.
	 *
	 * @param  array  $data
	 * @return Columbo\User
	 */
	protected function createUser($data=[])
	{
		return factory(User::class)->create($data);
	}

	/**
	 * Creates a trip. If data is passed, it is used
	 * 	and if no user is passed, it is created.
	 *
	 * @param  Columbo\User|null $user
	 * @param  array     $data
	 * @return Columbo\Trip
	 */
	protected function createTrip(User $user=null, $data=[])
	{
		if ($user == null) $user = $this->createUser();

		return $user->tripsOwner()->save(factory(Trip::class)->make($data));
	}

	protected function createTripMember(User $user, Trip $trip=null, $data=[])
	{
		if ($trip == null)	$trip = $this->createTrip($user);
		if ($data == [])	$data = ["join_date" => "2020-01-01", "leave_date" => "2021-01-01"];

		return $trip->members()->attach($user->id, $data);
	}

	/**
	 * Creates a report. If data is passed, it is used
	 * 	and if no trip is passed, it is created using
	 * 	the user given as first parameter or a new
	 * 	created user.
	 *
	 * @param  Columbo\User|null $user
	 * @param  Columbo\Trip|null $trip
	 * @param  array     $data
	 * @return Columbo\Report
	 */
	protected function createReport(User $user=null, Trip $trip=null, $data=[])
	{
		if ($user == null) $user = $this->createUser();
		if ($trip == null) $trip = $this->createTrip($user);

		$report = factory(Report::class)->make($data);
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

		return $report;
	}

	/**
	 * Creates a section. If data is passed, it is used
	 * 	and if no report is passed it is created using
	 * 	the used given as first parameter or a new
	 * 	created user.
	 *
	 * @param  Columbo\User|null   $user
	 * @param  Columbo\Report|null $report
	 * @param  array       $data
	 * @return Columbo\Section
	 */
	protected function createSection(User $user=null, Report $report=null, $data=[])
	{
		if ($user   == null) $user 	 = $this->createUser();
		if ($report == null) $report = $this->createReport($user);

		$section = factory(Section::class)->make($data);
        $section->owner()->associate($user);
        $section->report()->associate($report);
        $section->save();

		return $section;
	}

	/**
	 * Creates a location. If data is passed, it is used
	 * 	and if no user is passed it is created. If a
	 * 	trip is passed, it is used, and if no trip
	 * 	is passed it is created.
	 *
	 * @param  Columbo\User|null   $user
	 * @param  Columbo\Trip|null   $trip
	 * @return Columbo\Location
	 */
	protected function createLocation(User $user=null, Trip $trip=null, $data=[])
	{
		if ($user == null) $user = $this->createUser();
		if ($trip == null) $trip = $this->createTrip($user);

		$location = factory(Location::class)->make($data);
		$location->owner()->associate($user);
		$location->trip()->associate($trip);
		return $user->locations()->save($location);
	}

	protected function createRole($data=[])
	{
		return factory(Role::class)->create($data);
	}
}
