<?php

namespace Tests\Feature\Sections;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionAuthenticationTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function an_unauthenticated_user_cannot_create_a_section()
    {
        $user   = $this->createUser();
        $trip   = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $response = $this->expectJSON()
                         ->post("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/create", $this->getTestData());

        $this->assertUnauthenticated($response);
        $this->assertDatabaseMissing("sections", ["user_id"   => $user->id]);
        $this->assertDatabaseMissing("sections", ["report_id" => $report->id]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_read_a_section()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->get("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

        $this->assertUnauthenticated($response);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_update_a_section()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $update_data = $this->getTestDataWith(["content" => "another content"]);

        $response = $this->expectJSON()
                         ->put("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}", $update_data);

        $this->assertUnauthenticated($response);
        $this->assertDatabaseMissing("sections", ["content" => "another content"]);

        $response = $this->expectJSON()
                         ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}", $update_data);

        $this->assertUnauthenticated($response);
        $this->assertDatabaseMissing("sections", ["content" => "another content"]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete_a_section()
    {
        $user    = $this->createUser();
        $trip    = $this->createTrip($user);
        $report  = $this->createReport($user, $trip);
        $section = $this->createSection($user, $report);

        $response = $this->expectJSON()
                         ->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}/sections/{$section->id}");

        $this->assertUnauthenticated($response);
        $this->assertDatabaseHas("sections", ["id" => $section->id]);
    }

    private function getTestData()
    {
        return [
            "content"      => "Some content.",
            "visibility"   => "members",
            "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }
}
