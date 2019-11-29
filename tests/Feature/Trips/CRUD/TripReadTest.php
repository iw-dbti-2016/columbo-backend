<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

class TripReadTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function users_can_read_trip_details()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                         ->actingAs($user)
                         ->get("/api/v1/trips/{$trip->id}");

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure(false));
    }

    /** @test */
    public function users_can_get_trip_list()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->get("/api/v1/user/trips");

        $response->assertStatus(200);
        $response->assertJSONStructure($this->successStructure(false));
    }
}
