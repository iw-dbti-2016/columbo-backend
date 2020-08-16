<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	private $init = false;
	private $user;
	private $trip;


	public function setUp() : void
	{
		parent::setUp();

		$this->user = $this->createUser();
		$this->trip = $this->createTrip($this->user);

		Sanctum::actingAs($this->user);
	}

	/** @test */
	public function users_can_create_locations()
	{
		$response = $this->expectJSON()
						 ->post("/api/v1/trips/{$this->trip->id}/locations", $this->getTestAttributes());

		$response->assertStatus(201);
		$this->assertDatabaseHas("locations", ["user_id" => $this->user->id]);
	}

	protected function getTestAttributes()
	{
		return [
			"is_draft"     => 0,
			"coordinates"  => [
				"longitude" => 0.15,
				"latitude" => -9.15,
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
