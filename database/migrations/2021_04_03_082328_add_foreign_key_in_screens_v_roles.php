<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInScreensVRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('screens_v_roles', function (Blueprint $table) {
            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('screens_v_roles', function (Blueprint $table) {
            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }
}
