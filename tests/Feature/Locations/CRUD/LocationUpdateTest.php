<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationUpdateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_update_locations_they_own()
	{
		$this->withoutExceptionHandling();
		$user		= $this->createUser();
		$location	= $this->createLocation($user, null, $this->getTestAttributes());

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->patch("/api/v1/locations/{$location->id}", $this->getTestDataWith([
							 "name" => "New name",
						 ], $location->id));

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas("locations", ["name" => "New name"]);
		$this->assertDatabaseMissing("locations", ["name" => $location->name]);
	}

	/** @test */
	public function users_cannot_update_locations_they_don_not_own()
	{
		$user  = $this->createUser();
		$user2 = $this->createUser();
		$location  = $this->createLocation($user, null, $this->getTestAttributes());

		$response = $this->expectJSON()
							->actingAs($user2)
							->patch("/api/v1/locations/{$location->id}", $this->getTestDataWith([
								"name" => "New name",
							], $location->id));

		$this->assertUnauthorized($response);
		$this->assertDatabaseMissing("locations", ["name" => "New name"]);
		$this->assertDatabaseHas("locations", ["name" => $location->name]);
	}

	protected function getTestAttributes()
	{
		return [
			"is_draft"     => 0,
			"coordinates"  => [0.15, -9.15],
			"map_zoom"     => 2.245845,
			"name"         => "A new location",
			"info"         => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
			"visibility"   => "members",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "location";
	}
}
