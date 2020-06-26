<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->morphs('section_locationable', 'section_locationable_id_type_index');

            $table->timestamps();

            $table->foreign('section_id')
                    ->references('id')
                    ->on('sections')
                    ->onDelete('cascade');

            $table->primary(['section_locationable_id', 'section_locationable_type', 'section_id'], 'location_section_locationable_section_id_primary');
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
