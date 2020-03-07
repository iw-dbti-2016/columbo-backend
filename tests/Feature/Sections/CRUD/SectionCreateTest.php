<?php

namespace Tests\Feature\Sections\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Report;
use Columbo\Trip;
use Columbo\User;

class SectionCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_create_sections_in_their_reports()
	{
		// $this->withoutExceptionHandling();
		$user   = $this->createUser();
		$report = $this->createReport($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/sections", $this->getTestData(null, [
							"relationships" => [
								"owner" => [
									"type" => "user",
									"id"   => $user->id,
								],
								"report" => [
									"type" => "report",
									"id"   => $report->id,
								],
							],
						 ]));

		$response->assertStatus(201);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas("sections", ["user_id" => $user->id]);
		$this->assertDatabaseHas("sections", ["report_id" => $report->id]);
	}

	/** @test */
	public function users_cannot_create_sections_in_other_users_reports()
	{
		$user    = $this->createUser();
		$trip    = $this->createTrip($user);
		$report  = $this->createReport($user, $trip);
		$report2 = $this->createReport();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/sections", $this->getTestData(null, [
							"relationships" => [
								"owner" => [
									"type" => "user",
									"id"   => $user->id,
								],
								"report" => [
									"type" => "report",
									"id"   => $report2->id,
								],
							],
						 ]));

		$this->assertUnauthorized($response);
		$this->assertDatabaseMissing("sections", ["user_id" => $user->id]);
		$this->assertDatabaseMissing("sections", ["report_id" => $report->id]);
	}

	protected function getTestAttributes()
	{
		return [
			"content"      => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
			"visibility"   => "members",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "section";
	}
}
