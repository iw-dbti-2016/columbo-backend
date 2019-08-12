<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Owner
            $table->bigInteger('user_id')->unsigned()->index();

            /* DATA */
            $table->string('name', 100);
            $table->string('synopsis', 100)->nullable();
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');

            /* VISIBILITY */
            $table->enum('visibility', ['public', 'hidden', 'visitors', 'members', 'private'])->index();
            $table->timestamp('published_at');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('trips');
    }
}
