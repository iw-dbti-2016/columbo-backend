<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportDeleteTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_that_own_reports_can_delete_them()
	{
		$user   = $this->createUser();
		$report = $this->createReport($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->delete("/api/v1/reports/{$report->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructureWithoutData());

		$this->assertDatabaseHas("reports", [
			"id" => $report->id,
			"deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
		]);
	}

	/** @test */
	public function users_that_do_not_own_reports_cannot_delete_them()
	{
		$user   = $this->createUser();
		$user2  = $this->createUser();
		$report = $this->createReport($user);

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->delete("/api/v1/reports/{$report->id}");

		$this->assertUnauthorized($response);
		$this->assertDatabaseHas("reports", [
			"id" => $report->id,
			"deleted_at" => null,
		]);
	}
}

