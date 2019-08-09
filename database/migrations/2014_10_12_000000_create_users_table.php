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
            $table->string('image')->nullable();
            $table->point('home_location')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('description')->nullable();

            /* APPLICATION-RELATED DATA */
            $table->string('language', 10);
            $table->string('currency_preference')->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('currency_preference')
                    ->references('id')
                    ->on('currencies')
                    ->onDelete('set null');

            // Spatial index is impossible on the currently targeted server
            //$table->spatialIndex('home_location');
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
