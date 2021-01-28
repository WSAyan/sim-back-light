<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInProductOptionsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_options_details', function (Blueprint $table) {
            $table->foreign('product_options_id')->references('id')->on('product_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_options_details', function (Blueprint $table) {
            $table->foreign('product_options_id')->references('id')->on('product_options');
        });
    }
}
