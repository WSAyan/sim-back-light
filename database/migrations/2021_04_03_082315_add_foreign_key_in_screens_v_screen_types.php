<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInScreensVScreenTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('screens_v_screen_types', function (Blueprint $table) {
            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('screen_type_id')->references('id')->on('screen_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('screens_v_screen_types', function (Blueprint $table) {
            $table->foreign('screen_id')->references('id')->on('screens');
            $table->foreign('screen_type_id')->references('id')->on('screen_types');
        });
    }
}
