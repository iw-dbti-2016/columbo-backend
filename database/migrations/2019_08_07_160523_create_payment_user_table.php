<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_user', function (Blueprint $table) {
            $table->bigInteger('payment_id')->unsigned()->index();
            $table->bigInteger('user_id')->unsigned()->index();

            /* DATA */
            $table->integer('personal_amount')->unsigned()->default(0);
            $table->boolean('payed')->default(false);

            $table->timestamps();

            $table->foreign('payment_id')
                    ->references('id')
                    ->on('payments')
                    ->onDelete('cascade');

            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->primary(['payment_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_user');
    }
}
