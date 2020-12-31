<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Columbo\Role;

class RoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		foreach ($this->getRoleMap() as $role) {
			Role::create($role);
		}
	}

	public function getRoleMap()
	{
		return [
			[
				"label" => "owner",
				"name"  => "Owner",
			],
			[
				"label" => "admin",
				"name"  => "Administrator",
			],
			[
				"label" => "editor",
				"name"  => "Editor",
			],
			[
				"label" => "observer",
				"name"  => "Observer",
			],
			[
				"label" => "writer",
				"name"  => "Writer",
			],
		];
	}
}
