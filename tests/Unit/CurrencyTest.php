<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Columbo\Currency;

class CurrencyTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
    public function a_currency_can_be_created()
    {
        $currency = factory(Currency::class)->create();

        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
    }
}
