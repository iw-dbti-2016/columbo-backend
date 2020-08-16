<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ResourceFactory;
use Columbo\Currency;
use Columbo\Location;
use Columbo\Report;
use Columbo\Section;
use Columbo\Trip;
use Columbo\User;

class SectionTest extends TestCase
{
	use RefreshDatabase, ResourceFactory;

    /** @test */
    public function a_section_can_be_created()
    {
    	$section = $this->createSection();

        $this->assertDatabaseHas('sections', ['id' => $section->id]);
    }
}
