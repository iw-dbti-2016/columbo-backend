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

            $table->date('date');
            $table->text('program');

            $table->float('driving_distance_km')->default(0.0);
            $table->string('sleeping_location')->nullable();

            $table->enum('status_sleep', ['TODO', 'IN PROGRESS', 'PENDING', 'DONE'])->default('TODO');
            $table->enum('status_activities', ['TODO', 'IN PROGRESS', 'PENDING', 'DONE'])->default('TODO');

            $table->softDeletes();
            $table->timestamps();
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
