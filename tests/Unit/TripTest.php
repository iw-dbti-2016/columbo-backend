<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ResourceFactory;
use Columbo\Currency;
use Columbo\Report;
use Columbo\Section;
use Columbo\Trip;
use Columbo\User;

class TripTest extends TestCase
{
	use RefreshDatabase, ResourceFactory;

    /** @test */
    public function a_trip_can_be_created()
    {
    	$trip = $this->createTrip();

        $this->assertDatabaseHas('trips', ['id' => $trip->id]);
    }

    /** @test */
    public function a_trip_must_have_a_owner()
    {
        $user = $this->createUser();
        $trip = $this->createTrip($user);

        $this->assertDatabaseHas('trips', ['user_id' => $user->id]);
        $this->assertCount(1, $user->tripsOwner()->get());
    }

    /** @test */
    public function a_trip_can_have_reports()
    {
        $user = $this->createUser();
        $trip = $this->createTrip($user);
        $report = $this->createReport($user, $trip);

        $this->assertDatabaseHas('reports', ['trip_id' => $trip->id]);
        $this->assertDatabaseHas('reports', ['user_id' => $user->id]);
        $this->assertCount(1, $user->reports);
        $this->assertCount(1, $trip->reports);
    }

    /** @test */
    public function a_trip_can_have_members()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $user3 = $this->createUser();
        $role = $this->createRole();

        $trip = $this->createTrip($user1);

        $trip->members()->attach($user1, ["role_label" => $role->label]);
        $trip->members()->attach($user2, ["role_label" => $role->label]);
        $trip->members()->attach($user3, ["role_label" => $role->label]);
        $trip->save();

        $this->assertEquals($user1->tripsMember()->first()->id, $trip->id);
        $this->assertEquals($user2->tripsMember()->first()->id, $trip->id);
        $this->assertEquals($user3->tripsMember()->first()->id, $trip->id);
    }
}
