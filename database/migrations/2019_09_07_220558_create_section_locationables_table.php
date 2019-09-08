<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionLocationablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('section_locationables', function (Blueprint $table) {
            $table->bigInteger('section_id')->unsigned()->index();
            $table->morphs('locationable');

            /* DATA */
            $table->string('image', 100)->nullable();
            $table->time('time')->nullable();
            $table->integer('duration_minutes')->nullable();

            $table->timestamps();

            $table->foreign('section_id')
                    ->references('id')
                    ->on('sections')
                    ->onDelete('cascade');

            $table->primary(['locationable_id', 'locationable_type', 'section_id'], 'location_section_locationable_section_id_primary');
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
        Schema::dropIfExists('section_locationables');
    }
}
