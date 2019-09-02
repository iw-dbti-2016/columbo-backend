<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('trip_id')->unsigned()->nullable()->index();

            /* DATA */
            $table->string('name', 100)->unique();   // Name used in application
            $table->string('label', 100);            // Visual label for clients
            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('trip_id')
                    ->references('id')
                    ->on('trips');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
