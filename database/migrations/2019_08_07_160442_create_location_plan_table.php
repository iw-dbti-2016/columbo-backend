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
            $table->bigInteger('location_id')->unsigned()->index();
            $table->bigInteger('plan_id')->unsigned()->index();

            $table->text('description')->nullable();
            $table->time('time')->nullable();
            $table->integer('duration_minutes')->default(60);

            $table->timestamps();

            $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->onDelete('cascade');

            $table->foreign('plan_id')
                    ->references('id')
                    ->on('plans')
                    ->onDelete('cascade');

            $table->primary(['location_id', 'plan_id']);
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
