<?php

namespace Tests\Feature\Locations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationAuthenticationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function an_unauthenticated_user_cannot_create_a_location()
	{
		$response = $this->expectJSON()
						 ->post("/api/v1/locations", $this->getTestData());

		$this->assertUnauthenticated($response);
		$this->assertDatabaseMissing("locations", ["name" => "Cool trip"]);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_read_a_location()
	{
		$location = $this->createLocation();

		$response = $this->expectJSON()
						 ->get("/api/v1/locations/{$location->id}");

		$this->assertUnauthenticated($response);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_update_a_location()
	{
		$location = $this->createLocation();

		$response = $this->expectJSON()
						 ->patch("/api/v1/locations/{$location->id}", $this->getTestDataWith([
							"name" => "Testname 2",
						 ], $location->id));

		$this->assertUnauthenticated($response);
		$this->assertDatabaseMissing("trips", ["name" => "Testname 2"]);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_delete_a_location()
	{
		$location = $this->createLocation();

		$response = $this->expectJSON()
						 ->delete("/api/v1/locations/{$location->id}");

		$this->assertUnauthenticated($response);
		$this->assertDatabaseHas("locations", ["id" => $location->id]);
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
