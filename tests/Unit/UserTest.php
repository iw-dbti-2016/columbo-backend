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

class UserTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function a_user_can_be_created()
    {
        $user = factory(User::class)->create();

       	$this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function users_can_have_currency()
    {
    	$currency = factory(Currency::class)->create();

    	$user = factory(User::class)->create();
    	$user->currency()->associate($currency);
    	$user->save();

    	$this->assertEquals($currency->users()->first()->id, $user->id);
    	$this->assertDatabaseHas('users', ['currency_preference' => $currency->id]);
    }

    /** @test */
    public function users_must_not_have_a_preferred_currency()
    {
    	$user = factory(User::class)->create();

    	$this->assertNull($user->currency);
    }

    /** @test */
    public function a_user_can_have_a_trip()
    {
    	$user = factory(User::class)->create();

    	$trip = factory(Trip::class)->make();
        $trip->owner()->associate($user);
    	$trip->save();

    	$this->assertDatabaseHas('trips', ['user_id' => $user->id]);
    	$this->assertCount(1, $user->tripsOwner()->where('user_id', $user->id)->get());
    }

    /** @test */
    public function a_user_can_have_multiple_trips()
    {
    	$user = factory(User::class)->create();

    	$trip1 = $user->tripsOwner()->save(factory(Trip::class)->make());
    	$trip2 = $user->tripsOwner()->save(factory(Trip::class)->make());
    	$user->save();

    	$this->assertDatabaseHas('trips', ['user_id' => $user->id, 'id' => $trip1->id]);
    	$this->assertDatabaseHas('trips', ['user_id' => $user->id, 'id' => $trip2->id]);

    	$this->assertEquals($trip1->owner->id, $user->id);
    	$this->assertEquals($trip2->owner->id, $user->id);
    }
}
