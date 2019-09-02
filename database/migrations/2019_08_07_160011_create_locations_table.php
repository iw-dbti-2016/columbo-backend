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
            // Owner
            $table->bigInteger('user_id')->unsigned()->index();

            /* DATA */
            $table->boolean('is_draft')->default(true);
            $table->point('coordinates');
            $table->string('name', 100)->nullable();
            $table->text('info')->nullable();
            $table->string('image', 100)->nullable();    // Path to image on server

            /* VISIBILITY */
            $table->tinyInteger('visibility')->unsigned()->index();
            $table->timestamp('published_at');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users');

            $table->spatialIndex('coordinates');
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
