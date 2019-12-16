<?php

namespace Tests\Feature\Reports;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Report;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportAuthenticationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

    /** @test */
    public function an_unauthenticated_user_cannot_create_a_report()
    {
    	$trip = $this->createTrip();

        $response = $this->expectJSON()
                         ->post("/api/v1/trips/{$trip->id}/reports/create", $this->getTestData());

        $this->assertUnauthenticated($response);
        $this->assertDatabaseMissing("reports", ["title" => "Cool report"]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_read_a_report()
    {
        $user   = $this->createUser();
        $trip   = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $response = $this->expectJSON()
                         ->get("/api/v1/trips/{$trip->id}/reports/{$report->id}");

        $this->assertUnauthenticated($response);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_update_a_report()
    {
        $user   = $this->createUser();
        $trip   = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $response = $this->expectJSON()
        				 ->patch("/api/v1/trips/{$trip->id}/reports/{$report->id}", $this->getTestDataWith([
        			         "title" => "Testname 2",
        			     ]));

    	$this->assertUnauthenticated($response);
        $this->assertDatabaseMissing("trips", ["name" => "Testname 2"]);

        $response = $this->expectJSON()
                         ->put("/api/v1/trips/{$trip->id}/reports/{$report->id}", $this->getTestDataWith([
                             "name" => "Testname 2",
                         ]));

    	$this->assertUnauthenticated($response);
        $this->assertDatabaseMissing("trips", ["name" => "Testname 2"]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_delete_a_report()
    {
        $user   = $this->createUser();
        $trip   = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $response = $this->expectJSON()
        				 ->delete("/api/v1/trips/{$trip->id}/reports/{$report->id}");

    	$this->assertUnauthenticated($response);
        $this->assertDatabaseHas("reports", ["id" => $report->id]);
    }

    private function getTestData()
    {
    	return [
			"title"        => "Cool report",
			"date"         => Carbon::now()->addDays(1)->format("Y-m-d"),
			"description"  => "Blablablabla description blablabla",
			"visibility"   => "friends",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }
}
