<?php

namespace Columbo\Traits;

use Columbo\Role;
use Columbo\Trip;
use Columbo\TripUserRoleMember;
use Columbo\User;
use Illuminate\Support\Facades\Cache;


trait PolicyInformationPoint
{
	private static $PERMISSION_GLUE = ",";
	private static $CACHE_PREFIX = "permissions:";


	protected function authorize(String $permission, User $user, Trip $trip)
	{
		$permissions = $this->getPermissions($user, $trip);

		if (! $permissions) {
			return false;
		}

		return $this->isInPermissions($permission, $permissions);
	}

	private function isInPermissions(String $permission, String $permissions)
	{
		$permissions = explode(self::$PERMISSION_GLUE, $permissions);

		return in_array($permission, $permissions);
	}

	private function getPermissions(User $user, Trip $trip)
	{
		$role_label = $this->getRole($user, $trip);

		if (! $role_label) {
			return false;
		}

		$permissions = $this->getCachedPermissions($role_label);

		if (! $permissions) {
			return $this->fetchPermissions($role_label);
		}

		return $permissions;
	}

	protected function getRole(User $user, Trip $trip)
	{
		$roles = TripUserRoleMember::select('role_label')
					->where('trip_id', $trip->id)
					->where('user_id', $user->id)
					->get();

		if ($roles->count() != 1) {
			return false;
		}

		return $roles->first()->role_label;
	}

	private function fetchPermissions(String $role_label)
	{
		$permissions = Role::find($role_label)
							->permissions()
							->select("label")
							->get()
							->implode("label", self::$PERMISSION_GLUE);

		$this->cachePermissions($role_label, $permissions);

		return $permissions;
	}

	private function getCachedPermissions(String $role_label)
	{
		if (Cache::has(self::$CACHE_PREFIX . $role_label)) {
			return Cache::get(self::$CACHE_PREFIX . $role_label, false);
		}

		return false;
	}

	private function cachePermissions(String $role_label, String $permissions)
	{
		Cache::forever(self::$CACHE_PREFIX . $role_label, $permissions);
	}
}
