<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trip_id')->unsigned();
            $table->bigInteger('plan_id')->unsigned();

            $table->string('title');
            $table->text('description')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('trip_id')
                    ->references('id')
                    ->on('trips')
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
        Schema::dropIfExists('reports');
    }
}
