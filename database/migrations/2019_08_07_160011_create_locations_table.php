<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->bigIncrements('id');

            /* DATA */
            $table->point('coordinates');
            $table->string('name')->nullable();
            $table->text('info')->nullable();
            $table->string('image')->nullable();    // Path to image on server

            $table->softDeletes();
            $table->timestamps();

            // Spatial index is impossible on the currently targeted server
            //$table->spatialIndex('coordinates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
