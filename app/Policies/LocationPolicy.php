<?php

namespace Columbo\Policies;

use Columbo\Location;
use Columbo\Traits\PolicyInformationPoint;
use Columbo\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
	use HandlesAuthorization, PolicyInformationPoint;

	/**
	 * Determine whether the user can view any locations.
	 *
	 * @param  \Columbo\User  $user
	 * @return mixed
	 */
	public function viewAny(User $user=null)
	{
		return true;
	}

	/**
	 * Determine whether the user can view the location.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\Location  $location
	 * @return mixed
	 */
	public function view(User $user=null, Location $location)
	{
		return true;
	}

	/**
	 * Determine whether the user can create locations.
	 *
	 * @param  \Columbo\User  $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		return true;
	}

	/**
	 * Determine whether the user can update the location.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\Location  $location
	 * @return mixed
	 */
	public function update(User $user, Location $location)
	{
		return $location->user_id == $user->id;
	}

	/**
	 * Determine whether the user can delete the location.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\Location  $location
	 * @return mixed
	 */
	public function delete(User $user, Location $location)
	{
		return $location->user_id == $user->id;
	}

	/**
	 * Determine whether the user can restore the location.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\Location  $location
	 * @return mixed
	 */
	public function restore(User $user, Location $location)
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the location.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\Location  $location
	 * @return mixed
	 */
	public function forceDelete(User $user, Location $location)
	{
		//
	}
}
