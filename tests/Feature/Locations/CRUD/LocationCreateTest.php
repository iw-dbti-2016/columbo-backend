<?php

namespace Tests\Feature\Locations\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_cannot_create_locations_without_user_relationship()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/locations", $this->getTestData());

		$response->assertStatus(400);
		$response->assertJSONStructure($this->errorStructure());

		$this->assertDatabaseMissing("locations", ["user_id" => $user->id]);
	}

	/** @test */
	public function users_cannot_create_locations_with_wrong_user_relationship()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/locations", $this->getTestData(null, [
						 	"relationships" => [
						 		"owner" => [
									"type"	=> "user",
									"id"	=> ($user->id + 1),
						 		],
						 	],
						 ]));

		$response->assertStatus(400);
		$response->assertJSONStructure($this->errorStructure());

		$this->assertDatabaseMissing("locations", ["user_id" => $user->id]);
	}

	/** @test */
	public function users_can_create_locations()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/locations", $this->getTestData(null, [
						 	"relationships" => [
						 		"owner" => [
									"type"	=> "user",
									"id"	=> $user->id,
						 		],
						 	],
						 ]));

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
