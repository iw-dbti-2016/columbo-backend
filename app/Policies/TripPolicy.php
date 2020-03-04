<?php

namespace TravelCompanion\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use TravelCompanion\Trip;
use TravelCompanion\User;

class TripPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any trips.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function viewAny(User $user=null)
	{
		return true;
	}

	/**
	 * Determine whether the user can view the trip.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Trip  $trip
	 * @return mixed
	 */
	public function view(User $user=null, Trip $trip)
	{
		return true;
	}

	/**
	 * Determine whether the user can create trips.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return true;
	}

	public function addMembers(User $user, Trip $trip)
	{
		return true;
	}

	public function removeMembers(User $user, Trip $trip)
	{
		return true;
	}

	public function acceptInvite(User $user, Trip $trip)
	{
		return true;
	}

	public function declineInvite(User $user, Trip $trip)
	{
		return true;
	}

	/**
	 * Determine whether the user can update the trip.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Trip  $trip
	 * @return mixed
	 */
	public function update(User $user, Trip $trip)
	{
		return $trip->user_id == $user->id;
	}

	/**
	 * Determine whether the user can delete the trip.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Trip  $trip
	 * @return mixed
	 */
	public function delete(User $user, Trip $trip)
	{
		return $trip->user_id == $user->id;
	}

	/**
	 * Determine whether the user can restore the trip.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Trip  $trip
	 * @return mixed
	 */
	public function restore(User $user, Trip $trip)
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the trip.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Trip  $trip
	 * @return mixed
	 */
	public function forceDelete(User $user, Trip $trip)
	{
		//
	}
}
