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

class SectionDeleteTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_delete_sections_they_own()
	{
		$user    = $this->createUser();
		$section = $this->createSection($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->delete("/api/v1/sections/{$section->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure(["meta"]);

		$this->assertSoftDeleted("sections", ["id" => $section->id]);
	}

	/** @test */
	public function users_cannot_delete_sections_they_do_not_own()
	{
		$user    = $this->createUser();
		$user2   = $this->createUser();
		$section = $this->createSection($user);

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->delete("/api/v1/sections/{$section->id}");

		$this->assertUnAuthorized($response);
		$this->assertDatabaseHas("sections", [
			"id" => $section->id,
			"deleted_at" => null,
		]);
	}
}
