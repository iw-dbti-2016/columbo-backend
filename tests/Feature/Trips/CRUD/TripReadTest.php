<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Trip;
use Columbo\User;

class TripReadTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_read_trip_details()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->get("/api/v1/trips/{$trip->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());
	}

	/** @test */
	public function users_can_get_trip_list()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
							->actingAs($user)
							->get("/api/v1/user/trips");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successCollectionStructure());
	}
}
