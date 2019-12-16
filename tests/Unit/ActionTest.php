<?php

namespace Tests\Unit;

use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\ResourceFactory;
use TravelCompanion\Action;
use TravelCompanion\Currency;
use TravelCompanion\Location;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class ActionTest extends TestCase
{
    use RefreshDatabase, ResourceFactory;

    /** @test */
    public function an_action_can_have_a_user()
    {
        $user = $this->createUser();

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
        $user = $this->createUser();
        $trip = $this->createTrip($user);

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
        $user = $this->createUser();
        $report = $this->createReport($user);

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
        $user = $this->createUser();
        $section = $this->createSection($user);

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
        $user = $this->createUser();
        $location = $this->createLocation($user);

        $action = factory(Action::class)->make();
        $action->user()->associate($user);
        $action->subject()->associate($location);
        $action->save();

        $this->assertEquals($action->subject->id, $location->id);
        $this->assertEquals($location->actions()->first()->id, $action->id);
    }
}
