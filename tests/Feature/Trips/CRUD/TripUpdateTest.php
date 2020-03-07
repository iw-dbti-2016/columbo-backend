<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Trip;
use Columbo\User;

class TripUpdateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_update_trips_they_own()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user, $this->getTestAttributes());

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->patch("/api/v1/trips/{$trip->id}", $this->getTestDataWith([
							 "name" => "New name",
						 ], $trip->id));

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas("trips", ["name" => "New name"]);
		$this->assertDatabaseMissing("trips", ["name" => $trip->name]);
	}

	/** @test */
	public function users_cannot_update_trips_they_do_not_own()
	{
		$user  = $this->createUser();
		$trip  = $this->createTrip($user, $this->getTestAttributes());
		$user2 = $this->createUser();

		$response = $this->expectJSON()
							->actingAs($user2)
							->patch("/api/v1/trips/{$trip->id}", $this->getTestDataWith([
								"name" => "New name",
							], $trip->id));

		$this->assertUnauthorized($response);
		$this->assertDatabaseMissing("trips", ["name" => "New name"]);
		$this->assertDatabaseHas("trips", ["name" => $trip->name]);
	}

	protected function getTestAttributes() {
		return [
			"name"         => "Trip name",
			"synopsis"     => "Trip synopsis",
			"description"  => "Trip description",
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
