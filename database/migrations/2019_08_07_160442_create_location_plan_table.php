<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationPlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_plan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('location_id')->unsigned();
            $table->bigInteger('plan_id')->unsigned();

            $table->timestamps();

            $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->onDelete('cascade');

            $table->foreign('plan_id')
                    ->references('id')
                    ->on('plans')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_plan');
    }
}
