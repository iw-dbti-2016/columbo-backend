<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\User;

class TripCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_user_can_create_a_trip()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips", $this->getTestData());

		$response->assertStatus(201);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas('trips', [
			"id"      => $response->decodeResponseJson()["data"]["id"],
			"user_id" => $user->id,
		]);
	}

	protected function getTestAttributes()
	{
		return [
			"name"         => "Cool trip",
			"synopsis"     => "Chillin' in the Bahamas, duration: 1 month!",
			"description"  => "Blablablabla description blablabla",
			"start_date"   => Carbon::now()->format("Y-m-d"),
			"end_date"     => Carbon::now()->addMonths(1)->format("Y-m-d"),
			"visibility"   => "private",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "trip";
	}
}
