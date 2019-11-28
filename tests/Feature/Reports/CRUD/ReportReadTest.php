<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportReadTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

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
                            ->get("/api/v1/trips/{$trip->id}/reports/{$report->id}");

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
                            ->get("/api/v1/trips/{$trip2->id}/reports/{$report->id}");

        $response->assertStatus(404);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }
}
