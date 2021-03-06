<?php

namespace Tests\Feature\Trips;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Trip;
use Columbo\User;

/**
 * These tests test if actions are correctly blocked if users are not authorized.
 * All other tests (e.g. in ./CRUD/) assume that authorization is correcly handled.
 */
class TripAuthenticationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function an_unauthenticated_user_cannot_create_a_trip()
	{
		$response = $this->expectJSON()
						 ->post("/api/v1/trips", $this->getTestData());

		$this->assertUnauthenticated($response);
		$this->assertDatabaseMissing("trips", ["name" => "Cool trip"]);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_read_a_trip()
	{
		$trip = $this->createTrip();

		$response = $this->expectJSON()
						 ->get("/api/v1/trips/{$trip->id}");

		$this->assertUnauthenticated($response);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_update_a_trip()
	{
		$trip = $this->createTrip();

		$response = $this->expectJSON()
						 ->patch("/api/v1/trips/{$trip->id}", $this->getTestDataWith([
							"name" => "Testname 2",
						 ], $trip->id));

		$this->assertUnauthenticated($response);
		$this->assertDatabaseMissing("trips", ["name" => "Testname 2"]);
	}

	/** @test */
	public function an_unauthenticated_user_cannot_delete_a_trip()
	{
		$trip = $this->createTrip();

		$response = $this->expectJSON()
						 ->delete("/api/v1/trips/{$trip->id}");

		$this->assertUnauthenticated($response);
		$this->assertDatabaseHas("trips", ["id" => $trip->id]);
	}

	protected function getTestAttributes()
	{
		return [
			"name"         => "Cool trip",
			"synopsis"     => "Chillin' in the Bahamas, duration: 1 month!",
			"description"  => "Blablablabla description blablabla",
			"start_date"   => Carbon::now()->addDays(1)->format("Y-m-d"),
			"end_date"     => Carbon::now()->addMonths(1)->format("Y-m-d"),
			"visibility"   => "friends",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "trip";
	}
}
