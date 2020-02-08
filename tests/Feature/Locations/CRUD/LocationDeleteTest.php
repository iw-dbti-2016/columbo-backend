<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationDeleteTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_location_owner_can_delete_that_location()
	{
		$user		= $this->createUser();
		$location	= $this->createLocation($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->delete("/api/v1/locations/{$location->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure(["meta"]);

		$this->assertDatabaseHas("locations", [
			"id"         => $location->id,
			"deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
		]);
	}

	/** @test */
	public function a_location_not_owner_cannot_delete_that_location()
	{
		$user		= $this->createUser();
		$user2		= $this->createUser();
		$location	= $this->createLocation($user);

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->delete("/api/v1/locations/{$location->id}");

		$this->assertUnauthorized($response);
		$this->assertDatabaseHas("locations", [
			"id"         => $location->id,
			"deleted_at" => null,
		]);
	}
}
