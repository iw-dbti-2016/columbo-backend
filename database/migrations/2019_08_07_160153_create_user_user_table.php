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
            $table->bigInteger('user_1_id')->unsigned()->index();
            $table->bigInteger('user_2_id')->unsigned()->index();

            $table->boolean('confirmed')->default(false);
            $table->datetime('acceptance_date')->nullable();

            $table->date('meeting_date')->nullable();
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

            $table->primary(['user_1_id', 'user_2_id']);
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
