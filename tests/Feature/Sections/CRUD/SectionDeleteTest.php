<?php

namespace Tests\Feature\Sections\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionDeleteTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function users_can_delete_sections_they_own()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructureWithoutData());

        $this->assertSoftDeleted("sections", ["id" => $section->id]);
    }

    /** @test */
    public function users_cannot_delete_sections_they_do_not_own()
    {
        $user    = $this->createUser();
        $user2   = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user2)
                         ->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

        $this->assertUnAuthorized($response);
        $this->assertDatabaseHas("sections", [
            "id" => $section->id,
            "deleted_at" => null,
        ]);
    }

    /** @test */
    public function users_cannot_delete_sections_with_wrong_trip()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $trip2   = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->delete("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/{$section->id}");

        $this->assertNotFound($response);
        $this->assertDatabaseHas("sections", [
            "id" => $section->id,
            "deleted_at" => null,
        ]);
    }

    /** @test */
    public function users_cannot_delete_sections_with_wrong_report()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $report2 = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->delete("/api/v1/trips/{$trip->id}/reports/{$report2->id}/sections/{$section->id}");

        $this->assertNotFound($response);
        $this->assertDatabaseHas("sections", [
            "id" => $section->id,
            "deleted_at" => null,
        ]);
    }
}
