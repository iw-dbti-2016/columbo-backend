<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');

            /* DATA */
            $table->date('date');
            $table->text('program');

            $table->float('driving_distance_km')->nullable();
            $table->boolean('wifi_available')->default(true);
            $table->string('sleeping_location')->nullable();
            $table->integer('estimated_price')->unsigned()->nullable();
            $table->string('currency', 4)->nullable();

            /* STATUS */
            $table->enum('status_sleep', ['TODO', 'IN PROGRESS', 'PENDING', 'DONE'])->nullable();
            $table->enum('status_activities', ['TODO', 'IN PROGRESS', 'PENDING', 'DONE'])->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('currency')
                    ->references('id')
                    ->on('currencies')
                    ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
