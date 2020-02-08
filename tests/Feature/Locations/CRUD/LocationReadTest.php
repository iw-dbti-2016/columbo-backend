<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationReadTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_user_can_read_location_details()
	{
		$user		= $this->createUser();
		$location	= $this->createLocation($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->get("/api/v1/locations/{$location->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());
	}

	/** @test */
	public function a_user_can_read_the_closest_locations_to_given_coordinate()
	{

	}
}
