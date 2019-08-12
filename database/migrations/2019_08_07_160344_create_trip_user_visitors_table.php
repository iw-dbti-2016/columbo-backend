<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripUserVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_user_visitors', function (Blueprint $table) {
            $table->bigInteger('trip_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();

            /* DATA */
            $table->date('visit_start_date');
            $table->date('visit_end_date');

            /* VISIBILITY */
            $table->enum('visibility', ['public', 'hidden', 'private'])->index();

            $table->timestamps();

            $table->foreign('trip_id')
                    ->references('id')
                    ->on('trips')
                    ->onDelete('cascade');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->primary(['trip_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_user_visitors');
    }
}
