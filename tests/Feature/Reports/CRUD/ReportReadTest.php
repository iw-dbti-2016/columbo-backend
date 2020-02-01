<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportReadTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_retreive_reports()
	{
		$user   = $this->createUser();
		$user2  = $this->createUser();
		$trip   = $this->createTrip($user2);
		$report = $this->createReport($user2, $trip);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->get("/api/v1/reports/{$report->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());
	}

	/** @test */
	public function users_can_get_report_list()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->get("/api/v1/reports");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successCollectionStructure());
	}
}
