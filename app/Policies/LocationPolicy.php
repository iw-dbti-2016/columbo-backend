<?php

namespace TravelCompanion\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use TravelCompanion\Location;
use TravelCompanion\User;

class LocationPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any locations.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function viewAny(User $user=null)
	{
		return true;
	}

	/**
	 * Determine whether the user can view the location.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Location  $location
	 * @return mixed
	 */
	public function view(User $user=null, Location $location)
	{
		return true;
	}

	/**
	 * Determine whether the user can create locations.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return true;
	}

	/**
	 * Determine whether the user can update the location.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Location  $location
	 * @return mixed
	 */
	public function update(User $user, Location $location)
	{
		return $location->user_id == $user->id;
	}

	/**
	 * Determine whether the user can delete the location.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Location  $location
	 * @return mixed
	 */
	public function delete(User $user, Location $location)
	{
		return $location->user_id == $user->id;
	}

	/**
	 * Determine whether the user can restore the location.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Location  $location
	 * @return mixed
	 */
	public function restore(User $user, Location $location)
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the location.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Location  $location
	 * @return mixed
	 */
	public function forceDelete(User $user, Location $location)
	{
		//
	}
}
