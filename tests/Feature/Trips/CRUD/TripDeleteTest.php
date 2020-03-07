<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Trip;
use Columbo\User;

class TripDeleteTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_trip_owner_can_delete_that_trip()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->delete("/api/v1/trips/{$trip->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure(["meta"]);

		$this->assertDatabaseHas("trips", [
			"id"         => $trip->id,
			"deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
		]);
	}

	/** @test */
	public function a_trip_not_owner_cannot_delete_that_trip()
	{
		$user  = $this->createUser();
		$trip  = $this->createTrip($user);
		$user2 = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->delete("/api/v1/trips/{$trip->id}");

		$this->assertUnauthorized($response);
		$this->assertDatabaseHas("trips", [
			"id"         => $trip->id,
			"deleted_at" => null,
		]);
	}
}
