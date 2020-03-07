<?php

namespace Tests\Feature\Sections;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use Columbo\Report;
use Columbo\Trip;
use Columbo\User;

class SectionDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_section_cannot_be_created_with_invalid_data()
	{
		$user   = $this->createUser();
		$report = $this->createReport($user);

		$invalid_fields = $this->getInvalidFields();
		$responses = [];

		foreach($invalid_fields as $field) {
			$responses[] = $this->expectJSON()
								->actingAs($user)
								->post("/api/v1/sections", $this->getTestDataWith($field, null, [
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
		}

		foreach($responses as $response) {
			$this->assertValidationFailed($response);
		}

		$this->assertDatabaseMissing("sections", ["user_id" => $user->id]);
		$this->assertDatabaseMissing("sections", ["report_id" => $report->id]);
	}

	/** @test */
	public function a_section_cannot_be_updated_with_invalid_data()
	{
		$user    = $this->createUser();
		$section = $this->createSection($user);

		$invalid_fields = $this->getInvalidFields();
		$responses = [];

		foreach($invalid_fields as $field) {
			$update_data = array_merge(
				$this->getTestDataWith($field),
				["content" => "updated content"]
			);

			$response[] = $this->expectJSON()
							   ->actingAs($user)
							   ->patch("/api/v1/sections/{$section->id}", $update_data);
		}

		foreach ($responses as $response) {
			$this->assertValidationFailed($response);
		}

		$this->assertDatabaseMissing("sections", ["content" => "updated content"]);
	}

	/** @test */
	public function section_cannot_be_created_without_required_fields()
	{
		$user   = $this->createUser();
		$report = $this->createReport($user);

		$required_fields = ["visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$response[] = $this->expectJSON()
							   ->actingAs($user)
							   ->post("/api/v1/sections", $this->getTestDataWithout($field, null, [
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
		}

		foreach($responses as $response) {
			$this->assertValidationFailed($response);
		}

		$this->assertDatabaseMissing("sections", [
			"user_id" => $user->id,
			"report_id" => $report->id,
		]);
	}

	/** @test */
	public function a_section_cannot_be_updated_without_all_required_fields()
	{
		$user    = $this->createUser();
		$section = $this->createSection($user);

		$required_fields = ["visibility"];
		$responses = [];

		foreach($required_fields as $field) {
			$update_data = array_merge(
				$this->getTestDataWithout($field),
				["content" => "updated content"]
			);

			$response[] = $this->expectJSON()
							   ->actingAs($user)
							   ->patch("/api/v1/sections/{$section->id}", $update_data);
		}

		foreach($responses as $response) {
			$this->assertValidationFailed($response);
		}

		$this->assertDatabaseMissing("sections", ["content" => "updated content"]);
	}

	protected function getTestAttributes()
	{
		return [
			"content"      => "updated content",
			"visiblity"    => "friends",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "section";
	}

	private function getInvalidFields()
	{
		return [
			// Wrong Visibility
			["visibility"   => "random"],
			// Wrong published_at format
			["published_at" => Carbon::now()->format("d/m/Y H:i:s")],
		];
	}
}
