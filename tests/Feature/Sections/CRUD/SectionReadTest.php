<?php

namespace Tests\Feature\Sections\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionReadTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function users_can_read_section_details()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->get("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure(false));
    }

    /** @test */
    public function users_cannot_read_section_details_with_wrong_trip()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $trip2   = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->get("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/{$section->id}");

        $this->assertNotFound($response);
    }

    /** @test */
    public function users_cannot_read_section_details_with_wrong_report()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $report2 = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->get("/api/v1/trips/{$trip->id}/reports/{$report2->id}/sections/{$section->id}");

        $this->assertNotFound($response);
    }

    /** @test */
    public function users_can_read_section_lists()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->get("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections");

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure(false));
    }
}
