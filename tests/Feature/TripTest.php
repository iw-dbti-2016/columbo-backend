<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;
use TravelCompanion\Trip;
use TravelCompanion\User;

class TripTest extends TestCase
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

        // No published at
        $responses[] = $this->expectJSON()
                            ->actingAs($user)
                            ->post("/api/v1/trips/create", [
                                "name" => "Cool trip",
                                "synopsis" => "Chillin' in the Bahamas, duration: 1 month!",
                                "description" => "Blablablabla description blablabla",
                                "start_date" => Carbon::now()->format("Y-m-d"),
                                "end_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
                                "visibility" => "public",
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
    public function users_can_update_trips_they_own()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->patch("/api/v1/trips/" . $trip->id, [
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
                            ->patch("/api/v1/trips/" . $trip->id, [
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
                            ->patch("/api/v1/trips/" . $trip->id, [
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
                            ->patch("/api/v1/trips/" . $trip->id, [
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
                            ->patch("/api/v1/trips/" . $trip->id, [
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
                            ->patch("/api/v1/trips/" . $trip->id, [
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
                            ->patch("/api/v1/trips/" . $trip->id, [
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

    /** @test */
    public function a_trip_owner_can_delete_that_trip()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->delete("/api/v1/trips/" . $trip->id);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "message",
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
                            ->delete("/api/v1/trips/" . $trip->id);

        $response->assertStatus(403);
        $response->assertJSONStructure([
            "success",
            "message",
        ]);
    }

    /** @test */
    public function users_can_get_trip_details()
    {
        $user = factory(User::class)->create();
        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());

        $response = $this->expectJSON()
                            ->actingAs($user)
                            ->get("/api/v1/trips/" . $trip->id);

        $response->assertStatus(200);
        $response->assertJSONStructure([
            "success",
            "data",
        ]);
    }
}
