<?php

namespace Tests\Feature\Locations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	 /** @test */
	public function users_can_create_locations()
	{
		$this->withoutExceptionHandling();
		$user   = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/locations", $this->getTestData());

		$response->assertStatus(201);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas("locations", ["user_id" => $user->id]);
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
