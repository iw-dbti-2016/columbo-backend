<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ResourceFactory;
use TravelCompanion\Currency;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionTest extends TestCase
{
	use RefreshDatabase, ResourceFactory;

    /** @test */
    public function a_section_can_be_created()
    {
    	$section = $this->createSection();

        $this->assertDatabaseHas('sections', ['id' => $section->id]);
    }

    /** @test */
    public function a_section_can_have_a_location()
    {
        $section = $this->createSection();
        $location = $this->createLocation(null, $section);

        $this->assertDatabaseHas('locations', ['id' => $location->id]);
        $this->assertCount(1, $section->locationable()->get());
    }
}
