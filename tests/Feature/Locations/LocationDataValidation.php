<?php

namespace Tests\Feature\Locations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class LocationDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_location_cannot_be_created_with_invalid_data()
	{
		$user = $this->createUser();
		$responses = [];

		foreach($this->getInvalidFields() as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/locations", $this->getTestDataWith($field, null, [
									"relationships" => [
										"owner" => [
											"type" => "user",
											"id"   => $user->id
										],
									],
								]));
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("locations", ["user_id" => $user->id]);
	}

	/** @test */
	public function a_location_cannot_be_updated_with_invalid_data()
	{
		$user  = $this->createUser();
		$user2 = $this->createUser();
		$location  = $this->createLocation($user, null, $this->getTestAttributes());
		$responses = [];

		foreach($this->getInvalidFields() as $field) {
			$update_data = $this->getTestDataWith(array_merge($field, ["user_id" => $user2->id]));

			$responses[] = $this->expectJSON()
								->actingAs($user)
								->patch("/api/v1/locations/{$location->id}", $update_data);
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("locations", ["user_id" => $user2->id]);
	}

	/** @test */
	public function a_location_cannot_be_created_without_all_required_fields()
	{
		$user = $this->createUser();
		$required_fields = ["name", "start_date", "end_date", "visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/locations", $this->getTestDataWithout($field, null, [
									"relationships" => [
										"owner" => [
											"type" => "user",
											"id"   => $user->id
										],
									],
								]));
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("locations", ["user_id" => $user->id]);
	}

	/** @test */
	public function a_location_cannot_be_updated_without_all_required_fields()
	{
		$user  = $this->createUser();
		$user2 = $this->createUser();
		$location  = $this->createLocation($user, null, $this->getTestAttributes());

		$required_fields = ["name", "start_date", "end_date", "visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$update_data = array_merge($this->getTestDataWithout($field), ["user_id" => $user2->id]);

			$responses[] = $this->expectJSON()
								->actingAs($user)
								->patch("/api/v1/locations/{$location->id}", $update_data);
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("locations", ["user_id" => $user2->id]);
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

	private function getInvalidFields()
	{
		return [
			// Draft not boolean
			["is_draft" => "garbage is dirty!"],
			// Coordinates in wrong format
			["coordinates" => "1.025,2.587"],
			// Map zoon not float
			["map_zoom" => "large"],
			// Map zoom negative
			["map_zoom" => -50.4],
			// Name too long
			["name"         => "Cool location Cool location Cool location Cool location Cool location Cool location Cool location Cool location Cool location Cool location Cool location"],
			// Visibility invalid number
			["visibility"   => "random"],
			// Published at not timestamp
			["published_at" => Carbon::now()->format("Y-m-d")],
		];
	}
}
