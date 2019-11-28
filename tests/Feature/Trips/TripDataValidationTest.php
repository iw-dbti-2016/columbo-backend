<?php

namespace Tests\Feature\Trips;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\User;

class TripDataValidationTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function a_user_cannot_make_a_trip_with_invalid_data()
    {
        $user = factory(User::class)->create();

        $responses = [];

        // Name too long
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "members",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Synopsis too long
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month! Chillin' in the Bahamas, duration: 1 month! Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "public",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Date wrong format
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("d/m/Y"),
                                "end_date" => Carbon::now()->addMonths(1)->format("d/m/Y"),
                                "visibility" => "authenticated",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Visibility invalid number
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "random",
                                "published_at" => Carbon::now()->format("Y-m-d H:i:s"),
                            ]);

        // Published at not timestamp
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "public",
                                "published_at" => Carbon::now()->format("Y-m-d"),
                            ]);

        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure([
                "success",
                "message",
                "errors",
            ]);
        }

        $this->assertDatabaseMissing("trips", [
            "user_id" => $user->id,
        ]);
    }

    /** @test */
    public function a_trip_cannot_be_made_without_all_required_fields()
    {
        $user = factory(User::class)->create();

        $responses = [];

        // No name
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "public",
                                "published_at" => Carbon::now()->format("Y-m-d"),
                            ]);

        // No start date
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "public",
                                "published_at" => Carbon::now()->format("Y-m-d"),
                            ]);

        // No end date
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "visibility" => "public",
                                "published_at" => Carbon::now()->format("Y-m-d"),
                            ]);

        // No visibility
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "published_at" => Carbon::now()->format("Y-m-d"),
                            ]);

        // No published at (NOT REQUIRED, DEFAULT NOW())
        /*$responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "public",
                            ]);*/


        foreach ($responses as $response) {
            $response->assertStatus(422);
            $response->assertJSONStructure([
                "success",
                "message",
                "errors",
            ]);
        }

        $this->assertDatabaseMissing("trips", [
            "user_id" => $user->id,
        ]);
    }
}
