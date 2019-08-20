<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Location;

class LocationTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function a_location_can_be_created()
    {
        $location = factory(Location::class)->create();

        $this->assertDatabaseHas('locations', ['id' => $location->id]);
    }
}
