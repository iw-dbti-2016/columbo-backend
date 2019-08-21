<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use TravelCompanion\Currency;
use TravelCompanion\Payment;
use TravelCompanion\Report;
use TravelCompanion\Section;
use TravelCompanion\Trip;
use TravelCompanion\User;

class PaymentTest extends TestCase
{
	use RefreshDatabase;

    /** @test */
    public function a_payment_can_be_created()
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

        $payment = factory(Payment::class)->make();
        $payment->trip_id = $trip->id;
        $payment->section_id = $section->id;
        $payment->user_id = $user->id;
        $payment->currency = $currency->id;
        $payment->save();

        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
    }
}
