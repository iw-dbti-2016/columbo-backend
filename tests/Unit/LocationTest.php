<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Location;
use TravelCompanion\User;

class LocationTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function a_location_can_be_created()
    {
    	$user = factory(User::class)->create();
        $location = $user->locations()->save(factory(Location::class)->make());

        $this->assertDatabaseHas('locations', ['id' => $location->id]);
    }
}
