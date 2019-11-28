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
	public function authenticated_users_can_retreive_sections()
	{
		$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();
        $section = factory(Section::class)->make();

        $section->owner()->associate($user);
        $section->report()->associate($report);

        $section->save();

        $response = $this->expectJSON()
        					->actingAs($user)
        					->get("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

    	$response->assertStatus(200);
    	$response->assertJSONStructure([
    		"success",
    		"data",
        ]);
	}

	/** @test */
	public function unauthenticated_users_cannot_retrieve_sections()
	{
		$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();
        $section = factory(Section::class)->make();

        $section->owner()->associate($user);
        $section->report()->associate($report);

        $section->save();

        $response = $this->expectJSON()
        					->get("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

    	$response->assertStatus(401);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);
	}

	/** @test */
	public function users_cannot_retrieve_sections_with_wrong_trip()
	{
		$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $trip2 = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();
        $section = factory(Section::class)->make();

        $section->owner()->associate($user);
        $section->report()->associate($report);

        $section->save();

        $response = $this->expectJSON()
        					->actingAs($user)
        					->get("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/{$section->id}");

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);
	}

	/** @test */
	public function users_cannot_retrieve_sections_with_wrong_report()
	{
		$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();
        $report2 = factory(Report::class)->make();

        $report2->owner()->associate($user);
        $report2->trip()->associate($trip);

        $report2->save();
        $section = factory(Section::class)->make();

        $section->owner()->associate($user);
        $section->report()->associate($report);

        $section->save();

        $response = $this->expectJSON()
        					->actingAs($user)
        					->get("/api/v1/trips/{$trip->id}/reports/{$report2->id}/sections/{$section->id}");

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);
	}
}
