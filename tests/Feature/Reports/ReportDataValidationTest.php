<?php

namespace Tests\Feature\Reports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_report_cannot_be_created_with_invalid_data()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$invalid_fields = $this->getInvalidFields();
		$responses = [];

		foreach($invalid_fields as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/reports", $this->getTestDataWith($field, null, [
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
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("reports", ["trip_id" => $trip->id]);
		$this->assertDatabaseMissing("reports", ["user_id" => $user->id]);
	}

	/** @test */
	public function a_report_cannot_be_created_without_all_required_data()
	{
		$user = $this->createUser();
		$trip = $this->createTrip($user);

		$required_fields = ["title", "visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/reports", $this->getTestDataWithout($field, null, [
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
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("reports", ["trip_id" => $trip->id]);
		$this->assertDatabaseMissing("reports", ["user_id" => $user->id]);
	}

	/** @test */
	public function a_report_cannot_be_updated_with_invalid_data()
	{
		$user   = $this->createUser();
		$user2  = $this->createUser();
		$report = $this->createReport($user);

		$invalid_fields = $this->getInvalidFields();
		$responses = [];

		foreach($invalid_fields as $field) {
			$update_data = $this->getTestDataWith(
				array_merge($field, ["user_id" => $user2->id])
			);

			$responses[] = $this->expectJSON()
								->actingAs($user)
								->patch("/api/v1/reports/{$report->id}", $update_data);
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("reports", ["user_id" => $user2->id]);
	}

	/** @test */
	public function a_report_cannot_be_updated_without_all_required_data()
	{
		$user   = $this->createUser();
		$user2  = $this->createUser();
		$report = $this->createReport($user);

		$required_fields = ["title", "visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$update_data = array_merge($this->getTestDataWithout($field), ["user_id", $user2->id]);

			$responses[] = $this->expectJSON()
								->actingAs($user)
								->patch("/api/v1/reports/{$report->id}", $update_data);
		}

		foreach ($responses as $response) {
			$response->assertStatus(422);
			$response->assertJSONStructure($this->errorStructure());
		}

		$this->assertDatabaseMissing("reports", ["user_id" => $user2->id]);
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

	private function getInvalidFields()
	{
		return [
			// Title too long
			["title"        => "New report New report New report New report New report New report New report New report New report New report"],
			// Date wrong format
			["date"         => "01-jan-2019"],
			// Visibility illegal
			["visibility"   => "random"],
			// Published at wrong format
			["published_at" => Carbon::now()->format("d/m/Y H-m")],
		];
	}
}
