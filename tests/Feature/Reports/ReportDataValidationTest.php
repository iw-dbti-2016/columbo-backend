<?php

namespace Tests\Feature\Reports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
    public function users_cannot_make_reports_with_invalid_data()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $responses = [];

        // Title too long
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", [
                                "title" => "New report New report New report New report New report New report New report New report New report New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Date wrong format
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", [
                                "title" => "New report",
                                "date" => "01-jan-2019",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Visibility illegal
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "random",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Published at wrong format
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", [
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
                            ->post("/api/v1/trips/{$trip->id}/reports/create", [
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Visibility
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", [
                                "title" => "New report",
                                "date" => "2019-01-01",
                                "description" => "Blabla",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
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
}
