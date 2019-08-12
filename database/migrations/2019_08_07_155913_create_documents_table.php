<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Owner
            $table->bigInteger('user_id')->unsigned()->index();

            /* DATA */
            $table->string('name', 100);
            $table->string('document', 100);     // Path to document on server
            $table->morphs('documentable'); // Object to which document belongs

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
        Schema::dropIfExists('documents');
    }
}
