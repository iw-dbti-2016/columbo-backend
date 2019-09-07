<?php

namespace Tests\Unit;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Action;
use TravelCompanion\Currency;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ActionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_action_can_have_a_user()
    {
        $user = factory(User::class)->create();

        $action = factory(Action::class)->make();
        $action->user()->associate($user);
        $action->subject()->associate($user);
        $action->save();

        $this->assertEquals($action->subject->id, $user->id);
        $this->assertEquals($user->actions()->first()->id, $action->id);
    }

    /** @test */
    public function an_action_can_have_a_trip()
    {
        $user = factory(User::class)->create();
        $trip = factory(Trip::class)->make();
        $trip->owner()->associate($user);
        $trip->save();

        $action = factory(Action::class)->make();
        $action->user()->associate($user);
        $action->subject()->associate($trip);
        $action->save();

        $this->assertEquals($action->subject->id, $trip->id);
        $this->assertEquals($trip->actions()->first()->id, $action->id);
    }

    /** @test */
    public function an_action_can_have_a_report()
    {
        $user = factory(User::class)->create();

        $trip = factory(Trip::class)->make();
        $trip->owner()->associate($user);
        $trip->save();

        $report = factory(Report::class)->make();
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

        $action = factory(Action::class)->make();
        $action->user()->associate($user);
        $action->subject()->associate($report);
        $action->save();

        $this->assertEquals($action->subject->id, $report->id);
        $this->assertEquals($report->actions()->first()->id, $action->id);
    }

    /** @test */
    public function an_action_can_have_a_section()
    {
        $user = factory(User::class)->create();

        $trip = factory(Trip::class)->make();
        $trip->owner()->associate($user);
        $trip->save();

        $report = factory(Report::class)->make();
        $report->owner()->associate($user);
        $report->trip()->associate($trip);
        $report->save();

        $section = factory(Section::class)->make();
        $section->owner()->associate($user);
        $section->report()->associate($report);
        $section->save();

        $action = factory(Action::class)->make();
        $action->user()->associate($user);
        $action->subject()->associate($section);
        $action->save();

        $this->assertEquals($action->subject->id, $section->id);
        $this->assertEquals($section->actions()->first()->id, $action->id);
    }

    /** @test */
    public function an_action_can_have_a_location()
    {
        $user = factory(User::class)->create();
        $location = $user->locations()->save(factory(Location::class)->make());

        $action = factory(Action::class)->make();
        $action->user()->associate($user);
        $action->subject()->associate($location);
        $action->save();

        $this->assertEquals($action->subject->id, $location->id);
        $this->assertEquals($location->actions()->first()->id, $action->id);
    }
}
