<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationDeleteTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	private $user;
	private $trip;
	private $location;

	public function setUp() : void
	{
		parent::setUp();

		$this->user = $this->createUser();
		$this->trip = $this->createTrip($this->user);

		$this->location = $this->createLocation($this->user, $this->trip);
	}

	/** @test */
	public function a_location_owner_can_delete_that_location()
	{
		Sanctum::actingAs($this->user);

		$response = $this->expectJSON()
						 ->delete("/api/v1/trips/{$this->trip->id}/locations/{$this->location->id}");

		$response->assertStatus(200);

		$this->assertDatabaseHas("locations", ["id" => $this->location->id]);
		$this->assertDatabaseMissing("locations", [
			"id" => $this->location->id,
			"deleted_at" => null,
		]);
	}

	/** @test */
	public function a_location_not_owner_cannot_delete_that_location()
	{
		$randomUser	= $this->createUser();
		Sanctum::actingAs($randomUser);

		$response = $this->expectJSON()
						 ->delete("/api/v1/trips/{$this->trip->id}/locations/{$this->location->id}");

		$this->assertUnauthorized($response);
		$this->assertDatabaseHas("locations", [
			"id"         => $this->location->id,
			"deleted_at" => null,
		]);
	}
}
