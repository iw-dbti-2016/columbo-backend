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

class SectionUpdateTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function users_can_update_sections_they_own()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->put("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure());

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure());

        $this->assertDatabaseHas("sections", ["content" => "Updated"]);
    }

    /** @test */
    public function users_cannot_update_sections_they_do_not_own()
    {
        $user    = $this->createUser();
        $user2   = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user2)
                         ->put("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $this->assertUnAuthorized($response);
        $this->assertDatabaseMissing("sections", ["content" => "Updated"]);

        $response = $this->expectJSON()
                         ->actingAs($user2)
                         ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $this->assertUnAuthorized($response);
        $this->assertDatabaseMissing("sections", ["content" => "Updated"]);
    }

    /** @test */
    public function users_cannot_update_sections_with_wrong_trip()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $trip2   = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->put("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $this->assertNotFound($response);
        $this->assertDatabaseMissing("sections", ["content" => "Updated"]);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->patch("/api/v1/trips/{$trip2->id}/reports/{$report->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $this->assertNotFound($response);
        $this->assertDatabaseMissing("sections", ["content" => "Updated"]);
    }

    /** @test */
    public function users_cannot_update_sections_with_wrong_report()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $report2 = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->put("/api/v1/trips/{$trip->id}/reports/{$report2->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $this->assertNotFound($response);
        $this->assertDatabaseMissing("sections", ["content" => "Updated"]);

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->patch("/api/v1/trips/{$trip->id}/reports/{$report2->id}/sections/{$section->id}", $this->getTestDataWith([
                             "content" => "Updated",
                         ]));

        $this->assertNotFound($response);
        $this->assertDatabaseMissing("sections", ["content" => "Updated"]);
    }

    private function getTestData()
    {
        return [
            "content"      => "Updated",
            "visibility"   => "friends",
            "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }
}
