<?php

namespace TravelCompanion\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any reports.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function viewAny(User $user=null)
	{
		return true;
	}

	/**
	 * Determine whether the user can view the report.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Report  $report
	 * @return mixed
	 */
	public function view(User $user=null, Report $report)
	{
		return true;
	}

	/**
	 * Determine whether the user can create reports.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function create(User $user, Trip $trip)
	{
		return $trip->user_id == $user->id;
	}

	/**
	 * Determine whether the user can update the report.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Report  $report
	 * @return mixed
	 */
	public function update(User $user, Report $report)
	{
		return $report->user_id == $user->id;
	}

	/**
	 * Determine whether the user can delete the report.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Report  $report
	 * @return mixed
	 */
	public function delete(User $user, Report $report)
	{
		return $report->user_id == $user->id;
	}

	/**
	 * Determine whether the user can restore the report.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Report  $report
	 * @return mixed
	 */
	public function restore(User $user, Report $report)
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the report.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Report  $report
	 * @return mixed
	 */
	public function forceDelete(User $user, Report $report)
	{
		//
	}
}
