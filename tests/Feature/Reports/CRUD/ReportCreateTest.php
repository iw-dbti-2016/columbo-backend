<?php

namespace Tests\Feature\Reports\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportCreateTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
    public function unauthenticated_users_cannot_make_reports()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->post("/api/v1/trips/{$trip->id}/reports/create", $this->getTestData());

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function authenticated_users_can_make_reports_in_their_trips()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", $this->getTestData());

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "message",
            "data",
        ]);

        $this->assertDatabaseHas("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_make_reports_in_other_users_their_trips()
    {
        $user1 = factory(User::class)->create();
        $trip = $user1->tripsOwner()->save(factory(Trip::class)->make());
        $user2 = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user2)
                            ->post("/api/v1/trips/{$trip->id}/reports/create", $this->getTestData());

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("reports", [
            "trip_id" => $trip->id,
            "user_id" => $user2->id,
        ]);
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
}
