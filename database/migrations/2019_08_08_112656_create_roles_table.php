<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roles', function (Blueprint $table) {
			$table->string('label', 50)->primary();	// Label used in application
			// $table->bigInteger('trip_id')->unsigned()->nullable()->index();

			/* DATA */
			$table->string('name', 50);		// Name of the role
			$table->text('description')->nullable();

			$table->timestamps();

			// $table->foreign('trip_id')
			// 		->references('id')
			// 		->on('trips')
			// 		->onDelete('set null');

			// $table->primary(['trip_id', 'label']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('roles');
	}
}
