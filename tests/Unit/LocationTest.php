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
    public function a_location_can_belong_to_a_section()
    {
		$section  = $this->createSection();
		$location = $this->createLocation(null, $section);

    	$this->assertDatabaseHas('sections', ['locationable_id' => $location->id]);
    	$this->assertCount(1, $location->sections()->get());
    }
}
