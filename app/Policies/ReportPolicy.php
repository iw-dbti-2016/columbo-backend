<?php

namespace Columbo\Policies;

use Columbo\Report;
use Columbo\Traits\PolicyInformationPoint;
use Columbo\Trip;
use Columbo\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
	use HandlesAuthorization, PolicyInformationPoint;

	public function viewAny(User $user, Trip $trip)
	{
		return $this->authorize('report:all:view', $user, $trip);
	}

	public function view(User $user, Report $report, Trip $trip)
	{
		return $this->authorize('report:all:view', $user, $trip);
	}

	public function create(User $user, Trip $trip)
	{
		return $this->authorize('report:create', $user, $trip);
	}

	public function update(User $user, Report $report, Trip $trip)
	{
		if ($report->user_id == $user->id) {
			return $this->authorize('report:own:edit', $user, $trip);
		}

		return $this->authorize('report:all:edit', $user, $trip);
	}

	public function delete(User $user, Report $report, Trip $trip)
	{
		if ($report->user_id == $user->id) {
			return $this->authorize('report:own:remove', $user, $trip);
		}

		return $this->authorize('report:all:remove', $user, $trip);
	}

	public function restore(User $user, Report $report)
	{
		return false;
	}

	public function forceDelete(User $user, Report $report)
	{
		return false;
	}
}
