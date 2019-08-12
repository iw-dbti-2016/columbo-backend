<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->bigIncrements('id');

            /* DATA */
            $table->string('name', 100)->nullable();
            $table->string('url');
            $table->morphs('linkable'); // Object to which link belongs

            /* VISISBILITY */
            $table->enum('visibility', ['public', 'hidden', 'private'])->index();
            $table->timestamp('published_at');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
}
