<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInProductsVOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_v_options', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_options_id')->references('id')->on('product_options');
            $table->foreign('product_options_details_id')->references('id')->on('product_options_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_v_options', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_options_id')->references('id')->on('product_options');
            $table->foreign('product_options_details_id')->references('id')->on('product_options_details');
        });
    }
}
