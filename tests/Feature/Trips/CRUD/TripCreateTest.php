<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\User;

class TripCreateTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function an_unauthenticated_user_cannot_make_a_trip()
    {
        $response = $this->expectJSON()
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "friends",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(401);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function a_user_can_make_a_trip()
    {
        $user = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "private",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(201);
        $response->assertJSONStructure([
            "success",
            "message",
            "data",
        ]);

        $this->assertDatabaseHas('trips', [
            "user_id" => $user->id,
            "name" => "Cool trip",
            "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
            "description" => "Blablablabla description blablabla",
            "start_date" => Carbon::now()->format("Y-m-d"),
            "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
            "visibility" => config("mapping.visibility")["private"],
        ]);
    }
}
