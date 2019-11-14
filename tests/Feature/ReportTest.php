<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    // GET              trips/{trip}/reports

    // POST /api/v1/    trips/{trip}/reports/create
    // GET              trips/{trip}/reports/{report}
    // PUT/PATCH        trips/{trip}/reports/{report}
    // DELETE           trips/{trip}/reports/{report}
    // (voordeel: andere tabellen gewoon in url gegeven)
    // (nadeel: moet controleren of alle stukken in url correct zijn)
    // (nadeel: trips/{trip}/reports/{report}/sections/{section}/locations/{location} is wel heel erg lang)

    // VS

    // POST /api/v1/    trips/{trip}/reports/create
    // GET              /reports/{report}
    // PUT/PATCH        /reports/{report}
    // DELETE           /reports/{report}
    // (voordeel: lengte)
    // (nadeel: expliciet andere tabellen opzoeken)
    // Dit systeem gebruiken, bij invoegen nieuwe record
    // (en updaten) zorgen dat visibility gelijk is of
    // strikter dan de bovenliggende record (in dit geval
    // de trip, maar voor een sectie de report bv.)
    // Dan hoeven we enkel naar de visibility te kijken
    // van het huidige item en de permissie, niet naar
    // de visibility van de bovenliggende items
    //
    // Óf we kijken effectief alleen naar de visibility
    // en staan toe dat iemand een report maakt dat prive
    // is en daarin een publieke sectie maakt die hij/zij
    // dan kan delen?

    /** @test */
    public function unauthenticated_users_cannot_make_reports()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function authenticated_users_can_make_reports_in_their_trips()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "message",
            "data",
        ]);

        $this->assertDatabaseHas("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_make_reports_in_other_users_their_trips()
    {
        $user1 = factory(User::class)->create();
        $trip = $user1->tripsOwner()->save(factory(Trip::class)->make());
        $user2 = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user2)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user2->id,
        ]);
    }

    /** @test */
    public function users_cannot_make_reports_with_invalid_data()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $responses = [];

        // Title too long
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report New report New report New report New report New report New report New report New report New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Date wrong format
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "01-jan-2019",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Visibility illegal
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "random",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Published at wrong format
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("d/m/Y H-m"),
                            ]);

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure([
                "success",
                "message",
                "errors",
            ]);
        }

        $this->assertDatabaseMissing("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function users_cannot_make_reports_without_all_required_data()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $responses = [];

        // Title
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Visibility
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Published at (NOT REQUIRED, DEFAULT NOW())
        /*$responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/" . $trip->id . "/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                            ]);*/

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure([
                "success",
                "message",
                "errors",
            ]);
        }

        $this->assertDatabaseMissing("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function users_can_retreive_reports()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $trip = $user2->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user2);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->get("/api/v1/trips/" . $trip->id . "/reports/" . $report->id);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);
    }

    /** @test */
    public function users_cannot_retreive_reports_if_wrong_trip_is_specified()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $trip1 = $user2->tripsOwner()->save(factory(Trip::class)->make());
        $trip2 = $user2->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user2);
        $report->trip()->associate($trip1);

        $report->save();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->get("/api/v1/trips/" . $trip2->id . "/reports/" . $report->id);

        $response->assertStatus(404);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function owners_of_reports_can_update_reports()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report->id, [
                                "title" => "New Title",
                                "date" => $report->date,
                                "description" => $report->description,
                                "visibility" => $report->visibility,
                                "published_at" => $report->published_at->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
            "data",
        ]);

        $this->assertDatabaseHas("reports", [
            "title" => "New Title",
        ]);
    }

    /** @test */
    public function non_owners_of_reports_cannot_update_reports()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $trip = $user2->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user2);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->patch("/api/v1/trips/" . $trip->id . "/reports/" . $report->id, [
                                "title" => "New Title",
                            ]);

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("reports", [
            "title" => "New Title",
        ]);
    }

    /** @test */
    public function users_that_own_reports_can_delete_them()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->delete("/api/v1/trips/" . $trip->id . "/reports/" . $report->id);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseHas("reports", [
            "id" => $report->id,
            "deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
    }

    /** @test */
    public function users_that_do_not_own_reports_cannot_delete_them()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();

        $report->owner()->associate($user);
        $report->trip()->associate($trip);

        $report->save();

        $response = $this->expectJSON()
                            ->actingAs($user2)
                            ->delete("/api/v1/trips/" . $trip->id . "/reports/" . $report->id);

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseHas("reports", [
            "id" => $report->id,
        ]);

        $this->assertDatabaseMissing("reports", [
            "id" => $report->id,
            "deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
    }
}
