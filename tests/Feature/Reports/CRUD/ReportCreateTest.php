<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Trip;
use Columbo\User;

class ReportCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_create_reports_in_their_trips()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/reports", $this->getTestData(null, [
							"relationships" => [
								"owner" => [
									"type" => "user",
									"id" => $user->id,
								],
								"trip" => [
									"type" => "trip",
									"id" => $trip->id,
								],
							],
						 ]));

		$response->assertStatus(201);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas("reports", [
			"trip_id" => $trip->id,
			"user_id" => $user->id,
		]);
	}

	/** @test */
	public function users_cannot_make_reports_in_other_users_their_trips()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);
		$user2 = $this->createUser();

		$response = $this->expectJSON()
							->actingAs($user2)
							->post("/api/v1/reports", $this->getTestData(null, [
								"relationships" => [
									"owner" => [
										"type" => "user",
										"id" => $user->id,
									],
									"trip" => [
										"type" => "trip",
										"id" => $trip->id,
									],
								],
							]));

		$this->assertUnauthorized($response);
		$this->assertDatabaseMissing("reports", [
			"trip_id" => $trip->id,
			"user_id" => $user2->id,
		]);
	}

	protected function getTestAttributes()
	{
		return [
			"title"        => "New report",
			"date"         => "2019-01-01",
			"description"  => "Blabla",
			"visibility"   => "private",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "report";
	}
}
