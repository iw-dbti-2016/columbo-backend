<?php

namespace Tests\Feature\Reports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportDataValidationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
    public function a_report_cannot_be_created_with_invalid_data()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $invalid_fields = $this->getInvalidFields();
        $responses = [];

        foreach($invalid_fields as $field) {
            $responses[] = $this->expectJSON()
                                ->actingAs($user)
                                ->post("/api/v1/trips/{$trip->id}/reports/create", $this->getTestDataWith($field));
        }

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure($this->errorStructure());
        }

        $this->assertDatabaseMissing("reports", ["trip_id" => $trip->id]);
        $this->assertDatabaseMissing("reports", ["user_id" => $user->id]);
    }

    /** @test */
    public function a_report_cannot_be_created_without_all_required_data()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $required_fields = ["title", "visibility"];
        $responses = [];

        foreach($required_fields as $field) {
            $responses[] = $this->expectJSON()
                                ->actingAs($user)
                                ->post("/api/v1/trips/{$trip->id}/reports/create", $this->getTestDataWithout($field));
        }

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure($this->errorStructure());
        }

        $this->assertDatabaseMissing("reports", ["trip_id" => $trip->id]);
        $this->assertDatabaseMissing("reports", ["user_id" => $user->id]);
    }

    /** @test */
    public function a_report_cannot_be_updated_with_invalid_data()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $report = factory(Report::class)->make();
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

        $invalid_fields = $this->getInvalidFields();
        $responses = [];

        foreach($invalid_fields as $field) {
            $update_data = $this->getTestDataWith(
                array_merge($field, ["user_id" => $user2->id])
            );

            $responses[] = $this->expectJSON()
                                ->actingAs($user)
                                ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}", $update_data);

            $responses[] = $this->expectJSON()
                                ->actingAs($user)
                                ->put("/api/v1/trips/{$trip->id}/reports/{$report->id}", $update_data);
        }

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure($this->errorStructure());
        }

        $this->assertDatabaseMissing("reports", ["user_id" => $user2->id]);
    }

    /** @test */
    public function a_report_cannot_be_updated_without_all_required_data()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $report = factory(Report::class)->make();
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

        $required_fields = ["title", "visibility"];
        $responses = [];

        foreach($required_fields as $field) {
            $update_data = array_merge($this->getTestDataWithout($field), ["user_id", $user2->id]);

            $responses[] = $this->expectJSON()
                                ->actingAs($user)
                                ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}", $update_data);

            $responses[] = $this->expectJSON()
                                ->actingAs($user)
                                ->put("/api/v1/trips/{$trip->id}/reports/{$report->id}", $update_data);
        }

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure($this->errorStructure());
        }

        $this->assertDatabaseMissing("reports", ["user_id" => $user2->id]);
    }

    private function getTestData()
    {
        return [
            "title"        => "New report",
            "date"         => "2019-01-01",
            "description"  => "Blabla",
            "visibility"   => "private",
            "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }

    private function getInvalidFields()
    {
        return [
            // Title too long
            ["title"        => "New report New report New report New report New report New report New report New report New report New report"],
            // Date wrong format
            ["date"         => "01-jan-2019"],
            // Visibility illegal
            ["visibility"   => "random"],
            // Published at wrong format
            ["published_at" => Carbon::now()->format("d/m/Y H-m")],
        ];
    }
}
