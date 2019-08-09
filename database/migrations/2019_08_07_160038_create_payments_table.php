<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trip_id')->unsigned()->index();
            // section_id == null => payment for trip, not for specific section
            $table->bigInteger('section_id')->unsigned()->nullable()->index();
            // Payer
            $table->bigInteger('user_id')->unsigned()->index();

            /* DATA */
            $table->datetime('date');
            $table->integer('total_amount')->unsigned();            // Amount with tax
            $table->string('currency', 4);
            $table->float('tax_percentage')->default(0.0);          // Tax percentage? Need tax amount or not needed at all?
            $table->integer('tip_amount')->unsigned()->default(0);  // Tip after tax

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('trip_id')
                    ->references('id')
                    ->on('trips')
                    ->onDelete('cascade');

            $table->foreign('section_id')
                    ->references('id')
                    ->on('sections')
                    ->onDelete('set null');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users');

            $table->foreign('currency')
                    ->references('id')
                    ->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
