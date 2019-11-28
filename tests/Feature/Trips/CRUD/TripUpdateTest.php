<?php

namespace Tests\Feature\Trips\CRUD;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

class TripUpdateTest extends TestCase
{
    use RefreshDatabase, APITestHelpers;

    /** @test */
    public function users_can_update_trips_they_own()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->patch("/api/v1/trips/{$trip->id}", [
                                "name" => "New name",
                                "synopsis" => "New synopsis",
                                "description" => $trip->description,
                                "start_date" => $trip->start_date,
                                "end_date" => $trip->end_date,
                                "visibility" => $trip->visibility,
                                "published_at" => $trip->published_at->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
            "data",
        ]);

        $this->assertDatabaseHas("trips", [
            "name" => "New name",
        ]);

        $this->assertDatabaseMissing("trips", [
            "name" => $trip->name,
        ]);
    }

    /** @test */
    public function users_cannot_update_trips_they_do_not_own()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $user2 = factory(User::class)->create();

        $response = $this->expectJSON()
                            ->actingAs($user2)
                            ->patch("/api/v1/trips/{$trip->id}", [
                                "name" => "New name",
                                "synopsis" => "New synopsis",
                                "description" => $trip->description,
                                "start_date" => $trip->start_date,
                                "end_date" => $trip->end_date,
                                "visibility" => $trip->visibility,
                                "published_at" => $trip->published_at->format("Y-m-d H:i:s"),
                            ]);

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);

        $this->assertDatabaseMissing("trips", [
            "name" => "New name",
        ]);

        $this->assertDatabaseHas("trips", [
            "name" => $trip->name,
        ]);
    }

    /** @test */
    public function users_cannot_update_trips_with_invalid_data()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $responses = [];

        // Name too long
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->patch("/api/v1/trips/{$trip->id}", [
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
                            ->patch("/api/v1/trips/{$trip->id}", [
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
                            ->patch("/api/v1/trips/{$trip->id}", [
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
                            ->patch("/api/v1/trips/{$trip->id}", [
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
                            ->patch("/api/v1/trips/{$trip->id}", [
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

        $this->assertDatabaseHas("trips", [
            "name" => $trip->name,
        ]);
    }
}
