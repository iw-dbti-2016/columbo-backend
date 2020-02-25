<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->string('permission_label', 50)->index();
            $table->string('role_label', 50)->index();

            $table->timestamps();

            $table->foreign('permission_label')
                    ->references('label')
                    ->on('permissions')
                    ->onDelete('cascade');

            $table->foreign('role_label')
                    ->references('label')
                    ->on('roles')
                    ->onDelete('cascade');

            $table->primary(['permission_label', 'role_label']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role');
    }
}
