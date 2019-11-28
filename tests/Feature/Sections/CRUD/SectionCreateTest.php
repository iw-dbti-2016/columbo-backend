<?php

namespace Tests\Feature\Sections\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

    /** @test */
    public function authenticated_user_can_create_sections()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "members",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
        	"success",
        	"message",
        	"data",
        ]);

        $this->assertDatabaseHas("sections", [
        	"user_id" => $user->id,
        	"report_id" => $report->id,
        ]);
    }

    /** @test */
    public function authenticated_user_cannot_create_section_if_trip_is_wrong()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $trip2 = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "members",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        $response->assertStatus(404);
        $response->assertJSONStructure([
        	"success",
        	"message",
        ]);

        $this->assertDatabaseMissing("sections", [
        	"user_id" => $user->id,
        	"report_id" => $report->id,
        ]);
    }

    /** @test */
    public function unauthenticated_users_cannot_make_sections()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
        					->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "members",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
        	"success",
        	"message",
        ]);

        $this->assertDatabaseMissing("sections", [
        	"user_id" => $user->id,
        	"report_id" => $report->id,
        ]);
    }
}
