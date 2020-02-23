<?php

namespace Tests\Feature\Trips\Relationships;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\Traits\APITestHelpers;

class MemberRelationTest extends TestCase
{
	use RefreshDatabase, APITestHelpers;

	/** @test */
	public function a_trip_creator_is_owning_member()
	{
		$user = $this->createUser();

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips", $this->getTestData(null, [
							"relationships" => [
								"owner" => [
									"type" => "user",
									"id"   => $user->id,
								],
							],
						 ]));

		$response->assertStatus(201);
		$response->assertJSONStructure($this->successStructure());

		$this->assertDatabaseHas('trips', [
			"id"      => $response->decodeResponseJson()["data"]["id"],
			"user_id" => $user->id,
		]);
		$this->assertDatabaseHas('trip_user_role_members', [
			"trip_id" => $response->decodeResponseJson()["data"]["id"],
			"user_id" => $user->id,
		]);
	}

	/** @test */
	public function a_user_can_add_a_member()
	{
		$user = $this->createUser();
		$user2 = $this->createUser();
		$user3 = $this->createUser();
		$user4 = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members", [
						 	"data" => [
						 		[
						 			"type" => "member",
						 			"id" => $user2->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 	],
						 ]);

		$response2 = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members", [
						 	"data" => [
						 		[
						 			"type" => "member",
						 			"id" => $user3->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 		[
						 			"type" => "member",
						 			"id" => $user4->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 	],
						 ]);

		$response->assertStatus(200);
		$response2->assertStatus(200);

		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user2->id]);
		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user3->id]);
		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user4->id]);
	}

	/** @test */
	public function a_user_can_update_a_member()
	{
		$user = $this->createUser();
		$user2 = $this->createUser();
		$user3 = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members", [
					 		"data" => [
					 			[
						 			"type" => "member",
						 			"id" => $user2->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
					 			],
					 		],
						 ]);

 		$this->assertDatabaseHas("trip_user_role_members", [
			"user_id" => $user2->id,
			"join_date"	=> Carbon::now()->format("Y-m-d"),
		]);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members", [
						 	"data" => [
						 		[
						 			"type" => "member",
						 			"id" => $user2->id,
						 			"join_date" => Carbon::now()->addDays(1)->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 		[
						 			"type" => "member",
						 			"id" => $user3->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 	],
						 ]);

		$this->assertDatabaseHas("trip_user_role_members", [
			"user_id" => $user2->id,
			"join_date"	=> Carbon::now()->addDays(1)->format("Y-m-d"),
		]);
		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user3->id]);
	}

	/** @test */
	public function a_user_can_remove_a_member()
	{
		$user = $this->createUser();
		$user2 = $this->createUser();
		$user3 = $this->createUser();
		$user4 = $this->createUser();
		$trip = $this->createTrip($user);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members", [
						 	"data" => [
						 		[
						 			"type" => "member",
						 			"id" => $user2->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 		[
						 			"type" => "member",
						 			"id" => $user3->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 		[
						 			"type" => "member",
						 			"id" => $user4->id,
						 			"join_date" => Carbon::now()->format("Y-m-d"),
						 			"leave_date" => Carbon::now()->addMonths(1)->format("Y-m-d"),
						 		],
						 	],
						 ]);

		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user2->id]);
		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user3->id]);
		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user4->id]);

		$response = $this->expectJSON()
						 ->actingAs($user)
						 ->delete("/api/v1/trips/{$trip->id}/relationships/members", [
						 	"data" => [
						 		[
						 			"type" => "member",
						 			"id" => $user2->id,
						 		],
						 		[
						 			"type" => "member",
						 			"id" => $user3->id,
						 		],
						 	],
						 ]);

		$this->assertDatabaseMissing("trip_user_role_members", ["user_id" => $user2->id]);
		$this->assertDatabaseMissing("trip_user_role_members", ["user_id" => $user3->id]);
		$this->assertDatabaseHas("trip_user_role_members", ["user_id" => $user4->id]);
	}

	/** @test */
	public function a_user_can_accept_a_membership_request()
	{
		$this->withoutExceptionHandling();

		$user = $this->createUser();
		$user2 = $this->createUser();
		$trip = $this->createTrip($user);
		$this->createTripMember($user2, $trip);

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members/accept");


		$response->assertStatus(200);
		$this->assertDatabaseHas("trip_user_role_members", [
			"user_id"             => $user2->id,
			"invitation_accepted" => true,
		]);
	}

	/** @test */
	public function a_user_can_decline_a_membership_request()
	{
		$this->withoutExceptionHandling();

		$user = $this->createUser();
		$user2 = $this->createUser();
		$trip = $this->createTrip($user);
		$this->createTripMember($user2, $trip);

		$response = $this->expectJSON()
						 ->actingAs($user2)
						 ->post("/api/v1/trips/{$trip->id}/relationships/members/decline");

		$response->assertStatus(200);
		$this->assertDatabaseMissing("trip_user_role_members", [
			"user_id" => $user2->id,
		]);
	}

	protected function getTestAttributes()
	{
		return [
			"name"         => "Cool trip",
			"synopsis"     => "Chillin' in the Bahamas, duration: 1 month!",
			"description"  => "Blablablabla description blablabla",
			"start_date"   => Carbon::now()->format("Y-m-d"),
			"end_date"     => Carbon::now()->addMonths(1)->format("Y-m-d"),
			"visibility"   => "private",
			"published_at" => Carbon::now()->format("Y-m-d H:i:s"),
		];
	}

	protected function getResourceType()
	{
		return "trip";
	}
}
