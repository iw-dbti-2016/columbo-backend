<?php

namespace Columbo\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Columbo\POI;
use Columbo\User;

class POIPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any p o i s.
	 *
	 * @param  \Columbo\User  $user
	 * @return mixed
	 */
	public function viewAny(User $user=null)
	{
		return true;
	}

	/**
	 * Determine whether the user can view the p o i.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\POI  $pOI
	 * @return mixed
	 */
	public function view(User $user=null, POI $pOI)
	{
		return true;
	}

	/**
	 * Determine whether the user can create p o i s.
	 *
	 * @param  \Columbo\User  $user
	 * @return mixed
	 */
	public function create(User $user)
	{
		//
	}

	/**
	 * Determine whether the user can update the p o i.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\POI  $pOI
	 * @return mixed
	 */
	public function update(User $user, POI $pOI)
	{
		//
	}

	/**
	 * Determine whether the user can delete the p o i.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\POI  $pOI
	 * @return mixed
	 */
	public function delete(User $user, POI $pOI)
	{
		//
	}

	/**
	 * Determine whether the user can restore the p o i.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\POI  $pOI
	 * @return mixed
	 */
	public function restore(User $user, POI $pOI)
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the p o i.
	 *
	 * @param  \Columbo\User  $user
	 * @param  \Columbo\POI  $pOI
	 * @return mixed
	 */
	public function forceDelete(User $user, POI $pOI)
	{
		//
	}
}
