<?php

namespace Tests\Feature\Trips;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

/**
 * These tests test if actions are correctly blocked if users are not authorized.
 * All other tests (e.g. in ./CRUD/) assume that authorization is correcly handled.
 */
class TripAuthenticationTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function an_unauthenticated_user_cannot_read_a_trip()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()->get("/api/v1/trips/{$trip->id}");

    	$response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_make_a_trip()
    {
        $response = $this->expectJSON()
                            ->post("/api/v1/trips/create", $this->getTestData());

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("trips", ["name" => "Cool trip"]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_update_a_trip()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
        					->patch("/api/v1/trips/{$trip->id}", $this->getTestDataWith([
        						"name" => "Testname 2",
        					]));

    	$response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("trips", ["name" => "Testname 2"]);

        $response = $this->expectJSON()
        					->put("/api/v1/trips/{$trip->id}", $this->getTestDataWith([
        						"name" => "Testname 2",
        					]));

    	$response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("trips", ["name" => "Testname 2"]);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_destroy_a_trip()
    {
    	$user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
        					->delete("/api/v1/trips/{$trip->id}");

    	$response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseHas("trips", ["id" => $trip->id]);
    }

    private function getTestData()
    {
    	return [
			"name"         => "Cool trip",
			"synopsis"     => "Chillin' in the Bahamas, duration: 1 month!",
			"description"  => "Blablablabla description blablabla",
			"start_date"   => Carbon::now()->addDays(1)->format("Y-m-d"),
			"end_date"     => Carbon::now()->addMonths(1)->format("Y-m-d"),
			"visibility"   => "friends",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ];
    }
}
