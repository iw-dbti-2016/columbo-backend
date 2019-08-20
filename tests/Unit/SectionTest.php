<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Currency;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class SectionTest extends TestCase
{
	use RefreshDatabase;

    /** @test */
    public function a_section_can_be_created()
    {
        $currency = factory(Currency::class)->create();
    	$user = factory(User::class)->make();
    	$user->currency_preference = $currency->id;
    	$user->save();

    	$trip = factory(Trip::class)->make();
    	$trip->user_id = $user->id;
    	$trip->save();

    	$report = factory(Report::class)->make();
    	$report->user_id = $user->id;
    	$report->trip_id = $trip->id;
    	$report->save();

    	$section = factory(Section::class)->make();
    	$section->user_id = $user->id;
    	$section->report_id = $report->id;
    	$section->save();

        $this->assertDatabaseHas('sections', ['id' => $section->id]);
    }

    /** @test */
    public function a_section_can_have_a_location()
    {
        $user = factory(User::class)->create();

        $trip = $user->tripsOwner()->save(factory(Trip::class)->make());
        $report = factory(Report::class)->make();
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

        $section = factory(Section::class)->make();
        $section->owner()->associate($user);
        $section->report()->associate($report);
        $section->save();

        $location = factory(Location::class)->create();
        $location->sections()->attach($section);
        $location->save();

        $this->assertDatabaseHas('locations', ['id' => $location->id]);
        $this->assertCount(1, $section->locations()->get());
    }
}
