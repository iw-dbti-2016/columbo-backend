<?php

namespace Tests\Feature\Sections\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionUpdateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_update_sections_they_own()
	{
		$user    = $this->createUser();
		$section = $this->createSection($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->patch("/api/v1/sections/{$section->id}", $this->getTestDataWith([
							 "content" => "Updated",
						 ]));

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas("sections", ["content" => "Updated"]);
	}

	/** @test */
	public function users_cannot_update_sections_they_do_not_own()
	{
		$user    = $this->createUser();
		$user2   = $this->createUser();
		$section = $this->createSection($user);

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->patch("/api/v1/sections/{$section->id}", $this->getTestDataWith([
							 "content" => "Updated",
						 ]));

		$this->assertUnAuthorized($response);
		$this->assertDatabaseMissing("sections", ["content" => "Updated"]);
	}

	protected function getTestAttributes()
	{
		return [
			"content"      => "Updated",
			"visibility"   => "friends",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "section";
	}
}
