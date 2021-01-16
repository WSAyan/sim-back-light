<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInProductsVImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_v_images', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('image_id')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_v_images', function (Blueprint $table) {
            $table->dropForeign('product_id');
            $table->dropForeign('image_id');
        });
    }
}
