<?php

namespace TravelCompanion\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\User;

class SectionPolicy
{
	use HandlesAuthorization;

	/**
	 * Determine whether the user can view any sections.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function viewAny(User $user)
	{
		return true;
	}

	/**
	 * Determine whether the user can view the section.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Section  $section
	 * @return mixed
	 */
	public function view(User $user, Section $section)
	{
		return true;
	}

	/**
	 * Determine whether the user can create sections.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @return mixed
	 */
	public function create(User $user, Report $report)
	{
		return $report->user_id == $user->id;
	}

	/**
	 * Determine whether the user can update the section.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Section  $section
	 * @return mixed
	 */
	public function update(User $user, Section $section)
	{
		return $section->user_id == $user->id;
	}

	/**
	 * Determine whether the user can delete the section.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Section  $section
	 * @return mixed
	 */
	public function delete(User $user, Section $section)
	{
		return $section->user_id == $user->id;
	}

	/**
	 * Determine whether the user can restore the section.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Section  $section
	 * @return mixed
	 */
	public function restore(User $user, Section $section)
	{
		//
	}

	/**
	 * Determine whether the user can permanently delete the section.
	 *
	 * @param  \TravelCompanion\User  $user
	 * @param  \TravelCompanion\Section  $section
	 * @return mixed
	 */
	public function forceDelete(User $user, Section $section)
	{
		//
	}
}
