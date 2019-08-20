<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Currency;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ReportTest extends TestCase
{
	use RefreshDatabase;

    /** @test */
    public function a_report_can_be_created()
    {
    	$user = factory(User::class)->create();

    	$trip = factory(Trip::class)->make();
    	$trip->user_id = $user->id;
    	$trip->save();

    	$report = factory(Report::class)->make();
    	$report->user_id = $user->id;
    	$report->trip_id = $trip->id;
    	$report->save();

        $this->assertDatabaseHas('reports', ['id' => $report->id]);
    }

    /** @test */
    public function a_report_can_have_sections()
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

        $this->assertDatabaseHas('sections', ['user_id' => $user->id, 'report_id' => $report->id]);
        $this->assertCount(1, $report->sections);
        $this->assertCount(1, $user->sections);
    }
}
