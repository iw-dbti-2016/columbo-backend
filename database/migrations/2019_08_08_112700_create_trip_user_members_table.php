<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripUserMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_user_role_members', function (Blueprint $table) {
            $table->bigInteger('trip_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->string('role_label', 50)->index();

            /* DATA */
            $table->boolean('invitation_accepted')->default(false);
            $table->date('join_date')->nullable();
            $table->date('leave_date')->nullable();

            $table->timestamps();

            $table->foreign('trip_id')
                    ->references('id')
                    ->on('trips')
                    ->onDelete('cascade');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->foreign('role_label')
                    ->references('label')
                    ->on('roles')
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
        Schema::dropIfExists('trip_user_members');
    }
}
