<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            /* BASIC DATA */
            $table->string('first_name', 50);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 50);
            $table->string('username', 40)->unique();
            $table->string('email', 80)->unique();

            /* OPTIONAL DATA */
            $table->string('telephone', 40)->nullable();
            $table->string('image', 100)->nullable();
            $table->point('home_location'); // NOT nullable for possible spatial index
            $table->date('birth_date')->nullable();
            $table->text('description')->nullable();

            /* APPLICATION-RELATED DATA */
            $table->string('language', 10)->nullable();
            $table->string('currency_preference', 4)->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('currency_preference')
                    ->references('id')
                    ->on('currencies');

            $table->spatialIndex('home_location');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
