<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationReadTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	private $user;
	private $trip;
	private $location;

	public function setUp() : void
	{
		parent::setUp();

		$this->user		= $this->createUser();
		$this->trip		= $this->createTrip($this->user);
		$this->location	= $this->createLocation($this->user, $this->trip);

		Sanctum::actingAs($this->user);
	}

	/** @test */
	public function a_user_can_read_location_details()
	{

		$response = $this->expectJSON()
						 ->get("/api/v1/trips/{$this->trip->id}/locations/{$this->location->id}");

		$response->assertStatus(200);
	}
}
