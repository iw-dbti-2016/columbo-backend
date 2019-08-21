<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Currency;
use TravelCompanion\Plan;

class PlanTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function a_plan_can_be_created()
    {
    	$currency = factory(Currency::class)->create();

        $plan = factory(Plan::class)->make();
        $plan->currency = $currency->id;
        $plan->save();

        $this->assertDatabaseHas('plans', ['id' => $plan->id]);
    }
}
