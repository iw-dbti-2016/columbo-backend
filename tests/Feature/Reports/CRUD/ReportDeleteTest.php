<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportDeleteTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

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
                            ->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}");

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
                            ->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}");

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

