<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// 2019_08_09_004946
class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            // Currency code (USD, EUR, ...)
            $table->string('id', 4);

            // Full-text name (United States Dollar, Euro, ...)
            $table->string('name', 30);
            $table->string('symbol', 4);

            /* COMPLEX CURRENCY SUPPORT FIELDS */
            $table->tinyInteger('decimals')->unsigned()->default(2);
            $table->char('decimal_spacer', 1)->default(',');
            $table->char('thousand_spacer', 1)->default(' ');

            $table->timestamps();

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
