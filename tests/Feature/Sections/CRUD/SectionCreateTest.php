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
    public function users_can_create_sections_in_their_reports()
    {
        $user   = $this->createUser();
        $trip   = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", $this->getTestData());

        $response->assertStatus(201);
        $response->assertJSONStructure($this->successStructure());

        $this->assertDatabaseHas("sections", ["user_id" => $user->id]);
        $this->assertDatabaseHas("sections", ["report_id" => $report->id]);
    }

    /** @test */
    public function users_cannot_create_sections_in_other_users_reports()
    {
        $user   = $this->createUser();
        $trip   = $this->createTrip($user);
        $trip2  = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->post("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/create", $this->getTestData());

        $this->assertNotFound($response);
        $this->assertDatabaseMissing("sections", ["user_id" => $user->id]);
        $this->assertDatabaseMissing("sections", ["report_id" => $report->id]);
    }

    private function getTestData()
    {
        return [
            "content"      => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. A quas corporis asperiores quos alias, maxime molestiae quibusdam. Quo, voluptates, animi.",
            "visibility"   => "members",
            "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }
}
