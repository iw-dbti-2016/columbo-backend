<?php

namespace Tests\Feature\Sections;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

    /** @test */
    public function section_cannot_be_created_with_invalid_data()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $responses = [];

        // Wrong visibility
        $response[] = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "random",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        // Wrong published_at format
        $response[] = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "members",
        						"published_at" => Carbon::now()->format("d/m/Y H:i:s"),
        					]);

        foreach ($responses as $response) {
        	$response->assertStatus(422);
        	$response->assertJSONStructure([
        		"success",
        		"message",
        		"errors",
        	]);
        }

        $this->assertDatabaseMissing("sections", [
        	"user_id" => $user->id,
        	"report_id" => $report->id,
        ]);
    }

    /** @test */
    public function section_cannot_be_created_without_required_fields()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $responses = [];

        // Visibility
        $response[] = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        // published_at (OPTIONAL, DEFAULT NOW)
        /*$response[] = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "members",
        					]);*/

        foreach ($responses as $response) {
        	$response->assertStatus(422);
        	$response->assertJSONStructure([
        		"success",
        		"message",
        		"errors",
        	]);
        }

        $this->assertDatabaseMissing("sections", [
        	"user_id" => $user->id,
        	"report_id" => $report->id,
        ]);
	}
}
