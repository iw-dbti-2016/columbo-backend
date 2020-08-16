<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationUpdateTest extends TestCase
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
		$this->location = $this->createLocation($this->user, $this->trip, $this->getTestAttributes());
	}

	/** @test */
	public function users_can_update_locations_they_own()
	{
		Sanctum::actingAs($this->user);

		$response = $this->expectJSON()
						 ->patch(
								"/api/v1/trips/{$this->trip->id}/locations/{$this->location->id}",
								$this->getTestAttributesWith(["name" => "New name"])
							);

		$response->assertStatus(200);

		$this->assertDatabaseHas("locations", ["name" => "New name"]);
		$this->assertDatabaseMissing("locations", ["name" => $this->location->name]);
	}

	/** @test */
	public function users_cannot_update_locations_they_don_not_own()
	{
		$randomUser = $this->createUser();
		Sanctum::actingAs($randomUser);

		$response = $this->expectJSON()
							->patch(
								"/api/v1/trips/{$this->trip->id}/locations/{$this->location->id}",
								$this->getTestDataWith(["name" => "New name"])
							);

		$this->assertUnauthorized($response);
		$this->assertDatabaseMissing("locations", ["name" => "New name"]);
		$this->assertDatabaseHas("locations", ["name" => $this->location->name]);
	}

	protected function getTestAttributes()
	{
		return [
			"is_draft"     => 0,
			"coordinates"  => [
				"latitude" => -9.15,
				"longitude" => 0.15,
			],
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
