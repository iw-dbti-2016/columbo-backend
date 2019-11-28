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
	public function users_can_destroy_sections_they_own()
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
        					->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

    	$response->assertStatus(200);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseHas("sections", [
            "id" => $section->id,
            "deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
	}

	/** @test */
	public function users_cannot_delete_sections_they_do_not_own()
	{
		$user = factory(User::class)->create();
		$user2 = factory(User::class)->create();
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
        					->actingAs($user2)
        					->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

    	$response->assertStatus(403);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseHas("sections", [
        	"id" => $section->id,
        ]);

        $this->assertDatabaseMissing("sections", [
            "id" => $section->id,
            "deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
	}

	/** @test */
	public function users_cannot_delete_sections_with_wrong_trip()
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
        					->delete("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/{$section->id}");

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseHas("sections", [
        	"id" => $section->id,
        ]);
	}

	/** @test */
	public function users_cannot_delete_sections_with_wrong_report()
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
        					->delete("/api/v1/trips/{$trip->id}/reports/{$report2->id}/sections/{$section->id}");

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseHas("sections", [
        	"id" => $section->id,
        ]);
	}
}
