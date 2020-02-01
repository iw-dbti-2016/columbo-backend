<?php

namespace Tests\Feature\Sections\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionReadTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function users_can_read_section_details()
	{
		$user    = $this->createUser();
		$section = $this->createSection($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->get("/api/v1/sections/{$section->id}");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successStructure());
	}

	/** @test */
	public function users_can_read_section_lists()
	{
		$this->withoutExceptionHandling();
		$user    = $this->createUser();
		$section = $this->createSection($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->get("/api/v1/sections");

		$response->assertStatus(200);
		$response->assertJSONStructure($this->successCollectionStructure());
	}
}
