<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

class TripDeleteTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function a_trip_owner_can_delete_that_trip()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->delete("/api/v1/trips/{$trip->id}");

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseHas("trips", [
            "id" => $trip->id,
            "deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
    }

    /** @test */
    public function a_trip_not_owner_cannot_delete_that_trip()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $user2 = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user2)
                            ->delete("/api/v1/trips/{$trip->id}");

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseHas("trips", [
            "id" => $trip->id,
        ]);

        $this->assertDatabaseMissing("trips", [
            "id" => $trip->id,
            "deleted_at" => Carbon::now()->format("Y-m-d H:i:s"),
        ]);
    }
}
