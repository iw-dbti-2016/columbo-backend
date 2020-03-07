<?php

namespace Tests\Feature\Trips;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\User;

class TripDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_trip_cannot_be_created_with_invalid_data()
	{
		$user = $this->createUser();
		$responses = [];

		foreach($this->getInvalidFields() as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/trips", $this->getTestDataWith($field, null, [
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

		$this->assertDatabaseMissing("trips", ["user_id" => $user->id]);
	}

	/** @test */
	public function a_trip_cannot_be_updated_with_invalid_data()
	{
		$user  = $this->createUser();
		$user2 = $this->createUser();
		$trip  = $this->createTrip($user, $this->getTestAttributes());
		$responses = [];

		foreach($this->getInvalidFields() as $field) {
			$update_data = $this->getTestDataWith(array_merge($field, ["user_id" => $user2->id]));

			$responses[] = $this->expectJSON()
								->actingAs($user)
								->patch("/api/v1/trips/{$trip->id}", $update_data);
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("trips", ["user_id" => $user2->id]);
	}

	/** @test */
	public function a_trip_cannot_be_created_without_all_required_fields()
	{
		$user = $this->createUser();
		$required_fields = ["name", "start_date", "end_date", "visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/trips", $this->getTestDataWithout($field, null, [
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

		$this->assertDatabaseMissing("trips", ["user_id" => $user->id]);
	}

	/** @test */
	public function a_trip_cannot_be_updated_without_all_required_fields()
	{
		$user  = $this->createUser();
		$user2 = $this->createUser();
		$trip  = $this->createTrip($user, $this->getTestAttributes());

		$required_fields = ["name", "start_date", "end_date", "visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$update_data = array_merge($this->getTestDataWithout($field), ["user_id" => $user2->id]);

			$responses[] = $this->expectJSON()
								->actingAs($user)
								->patch("/api/v1/trips/{$trip->id}", $update_data);
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("trips", ["user_id" => $user2->id]);
	}

	protected function getTestAttributes()
	{
		return [
			"name"         => "Cool trip",
			"synopsis"     => "Chillin' in the Bahamas, duration: 1 month!",
			"description"  => "Blablablabla description blablabla",
			"start_date"   => Carbon::now()->format("Y-m-d"),
			"end_date"     => Carbon::now()->addMonths(1)->format("Y-m-d"),
			"visibility"   => "members",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "trip";
	}

	private function getInvalidFields()
	{
		return [
			// Name too long
			["name"         => "Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip"],
			// Synopsis too long
			["synopsis"     => "Chillin' in the Bahamas, duration: 1 month! Chillin' in the Bahamas, duration: 1 month! Chillin' in the Bahamas, duration: 1 month!"],
			// Date wrong format
			["start_date"   => Carbon::now()->format("d/m/Y")],
			["end_date"     => Carbon::now()->addMonths(1)->format("d/m/Y")],
			// Visibility invalid number
			["visibility"   => "random"],
			// Published at not timestamp
			["published_at" => Carbon::now()->format("Y-m-d")],
		];
	}
}
