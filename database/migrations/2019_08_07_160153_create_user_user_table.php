<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_1_id')->unsigned();
            $table->bigInteger('user_2_id')->unsigned();
            $table->boolean('confirmed')->default(false);

            $table->datetime('acceptance_date')->nullable();
            $table->text('meeting_circumstance')->nullable();

            $table->timestamps();

            $table->foreign('user_1_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->foreign('user_2_id')
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
        Schema::dropIfExists('user_user');
    }
}
