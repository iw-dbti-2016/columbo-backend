<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ResourceFactory;
use Columbo\Location;
use Columbo\User;

class LocationTest extends TestCase
{
	use RefreshDatabase, ResourceFactory;

	/** @test */
    public function a_location_can_be_created()
    {
		$location = $this->createLocation();

        $this->assertDatabaseHas('locations', ['id' => $location->id]);
    }

    /** @test */
    public function a_location_must_have_a_owner()
    {
		$user     = $this->createUser();
		$location = $this->createLocation($user);

        $this->assertDatabaseHas('locations', ['user_id' => $user->id]);
        $this->assertCount(1, $user->locations()->get());
    }

    /** @test */
    public function a_location_belongs_to_a_trip()
    {
		$trip  = $this->createTrip();
		$location = $this->createLocation(null, $trip);

        $queried_trip = $location->trip()->first();
        $this->assertEquals($trip->id, $queried_trip->id);
    	$this->assertEquals(1, $location->trip()->count());
    }
}
