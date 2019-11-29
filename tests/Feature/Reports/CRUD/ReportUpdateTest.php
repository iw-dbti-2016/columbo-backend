<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportUpdateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

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
                         ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}", $this->getTestDataWith([
                             "title" => "New Title"
                         ]));

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure());

        $this->assertDatabaseHas("reports", ["title" => "New Title"]);
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
                         ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}", $this->getTestDataWith([
                             "title" => "New Title",
                         ]));

        $this->assertUnauthorized($response);
        $this->assertDatabaseMissing("reports", ["title" => "New Title"]);
    }

    private function getTestData()
    {
        return [
            "title"        => "Title",
            "date"         => Carbon::now()->addDays(1)->format("Y-m-d"),
            "description"  => "description",
            "visibility"   => "friends",
            "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }
}
