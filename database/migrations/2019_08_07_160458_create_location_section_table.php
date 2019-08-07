<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationSectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_section', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('location_id')->unsigned();
            $table->bigInteger('section_id')->unsigned();

            $table->timestamps();

            $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->onDelete('cascade');

            $table->foreign('section_id')
                    ->references('id')
                    ->on('sections')
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
        Schema::dropIfExists('location_section');
    }
}
