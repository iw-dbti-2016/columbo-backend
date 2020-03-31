<?php

namespace Columbo\Policies;

use Columbo\Traits\PolicyInformationPoint;
use Columbo\Trip;
use Columbo\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TripPolicy
{
	use HandlesAuthorization, PolicyInformationPoint;

	public function viewAny(?User $user)
	{
		return true;
	}

	public function view(User $user, Trip $trip)
	{
		return $this->authorize('trip:view', $user, $trip);
	}

	public function create(User $user)
	{
		return true;
	}

	public function addMembers(User $user, Trip $trip, String $role)
	{
		if ($role === "owner") {
			return false;
		} else if ($role === "admin") {
			return $this->authorize('members:add-admin', $user, $trip);
		}

		return $this->authorize('members:add', $user, $trip);
	}

	public function updateMembers(User $user, Trip $trip, String $role)
	{
		if ($role === "owner") {
			return $this->authorize('members:edit-owner', $user, $trip);
		} else if ($role === "admin") {
			return $this->authorize('members:edit-admin', $user, $trip);
		}

		return $this->authorize('members:edit', $user, $trip);
	}

	public function removeMembers(User $user, Trip $trip, String $role)
	{
		if ($role === "owner") {
			return false;
		} else if ($role === "admin") {
			return $this->authorize('members:remove-admin', $user, $trip);
		}

		return $this->authorize('members:remove', $user, $trip);
	}

	public function addVisitors(User $user, Trip $trip)
	{
		return $this->authorize('visitors:add', $user, $trip);
	}

	public function updateVisitors(User $user, Trip $trip)
	{
		return $this->authorize('visitors:update', $user, $trip);
	}

	public function removeVisitors(User $user, Trip $trip)
	{
		return $this->authorize('visitors:remove', $user, $trip);
	}

	public function acceptInvite(User $user, Trip $trip)
	{
		return true;
	}

	public function declineInvite(User $user, Trip $trip)
	{
		return true;
	}

	public function update(User $user, Trip $trip)
	{
		return $this->authorize('trip:edit', $user, $trip);
	}

	public function delete(User $user, Trip $trip)
	{
		return $this->authorize('trip:remove', $user, $trip);
	}

	public function restore(User $user, Trip $trip)
	{
		return false;
	}

	public function forceDelete(User $user, Trip $trip)
	{
		return false;
	}
}
