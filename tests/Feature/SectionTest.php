<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionTest extends TestCase
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
        					->post("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/create", [
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
        					->post("/api/v1/trips/" . $trip2->id . "/reports/" . $report->id . "/sections/create", [
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
        					->post("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/create", [
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
        					->post("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/create", [
        						"content" => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
        						"visibility" => "random",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        // Wrong published_at format
        $response[] = $this->expectJSON()
        					->actingAs($user)
        					->post("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/create", [
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
        					->post("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/create", [
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
        					->get("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id);

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
        					->get("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id);

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
        					->get("/api/v1/trips/" . $trip2->id . "/reports/" . $report->id . "/sections/" . $section->id);

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
        					->get("/api/v1/trips/" . $trip->id . "/reports/" . $report2->id . "/sections/" . $section->id);

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);
	}

	/** @test */
	public function users_can_update_sections_they_own()
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
        					->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id, [
        						"content" => "Updated",
        						"visibility" => $section->visibility,
        						"published_at" => $section->published_at->format("Y-m-d H:i:s"),
        					]);

    	$response->assertStatus(200);
    	$response->assertJSONStructure([
    		"success",
    		"message",
    		"data",
        ]);

        $this->assertDatabaseHas("sections", [
        	"content" => "Updated",
        ]);
	}

	/** @test */
	public function users_cannot_update_sections_they_do_not_own()
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
        					->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id, [
        						"content" => "Updated",
        						"visibility" => $section->visibility,
        						"published_at" => $section->published_at->format("Y-m-d H:i:s"),
        					]);

    	$response->assertStatus(403);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseMissing("sections", [
        	"content" => "Updated",
        ]);
	}

	/** @test */
	public function users_cannot_update_sections_with_wrong_trip()
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
        					->patch("/api/v1/trips/" . $trip2->id . "/reports/" . $report->id . "/sections/" . $section->id, [
        						"content" => "Updated",
        						"visibility" => $section->visibility,
        						"published_at" => $section->published_at->format("Y-m-d H:i:s"),
        					]);

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseMissing("sections", [
        	"content" => "Updated",
        ]);
	}

	/** @test */
	public function users_cannot_update_sections_with_wrong_report()
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
        					->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report2->id . "/sections/" . $section->id, [
        						"content" => "Updated",
        						"visibility" => $section->visibility,
        						"published_at" => $section->published_at->format("Y-m-d H:i:s"),
        					]);

    	$response->assertStatus(404);
    	$response->assertJSONStructure([
    		"success",
    		"message",
        ]);

        $this->assertDatabaseMissing("sections", [
        	"content" => "Updated",
        ]);
	}

	/** @test */
	public function users_cannot_update_sections_with_invalid_data()
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

        $responses = [];

        // Wrong visibility
        $response[] = $this->expectJSON()
        					->actingAs($user)
        					->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id, [
        						"content" => "Lorem ipsum.",
        						"visibility" => "random",
        						"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        					]);

        // Wrong published_at format
        $response[] = $this->expectJSON()
        					->actingAs($user)
        					->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id, [
        						"content" => "Lorem ipsum.",
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
        	"content" => "Lorem ipsum.",
        ]);
	}

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
        					->delete("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id);

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
        					->delete("/api/v1/trips/" . $trip->id . "/reports/" . $report->id . "/sections/" . $section->id);

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
        					->delete("/api/v1/trips/" . $trip2->id . "/reports/" . $report->id . "/sections/" . $section->id);

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
        					->delete("/api/v1/trips/" . $trip->id . "/reports/" . $report2->id . "/sections/" . $section->id);

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
