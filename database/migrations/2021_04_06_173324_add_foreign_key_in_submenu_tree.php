<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInSubmenuTree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submenu_tree', function (Blueprint $table) {
            $table->foreign('submenu_screen_id')->references('id')->on('screens');
            $table->foreign('parent_screen_id')->references('id')->on('screens');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submenu_tree', function (Blueprint $table) {
            $table->foreign('submenu_screen_id')->references('id')->on('screens');
            $table->foreign('parent_screen_id')->references('id')->on('screens');
        });
    }
}
