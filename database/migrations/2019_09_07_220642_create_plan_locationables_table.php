<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanLocationablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_locationables', function (Blueprint $table) {
            $table->bigInteger('plan_id')->unsigned()->index();
            $table->morphs('locationable');

            /* DATA */
            $table->text('description')->nullable();
            $table->time('time')->nullable();
            $table->integer('duration_minutes')->nullable();

            $table->timestamps();

            $table->foreign('plan_id')
                    ->references('id')
                    ->on('plans')
                    ->onDelete('cascade');

            $table->primary(['locationable_id', 'locationable_type', 'plan_id'], 'location_plan_locationable_plan_id_primary');
            $table->index(['locationable_id', 'locationable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_locationables');
    }
}
