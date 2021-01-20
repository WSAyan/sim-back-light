<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyInOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('salesperson_user_id')->references('id')->on('users');
            $table->foreign('delivery_method_id')->references('id')->on('delivery_methods');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('tax_id')->references('id')->on('tax');
            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->foreign('payment_status_id')->references('id')->on('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('salesperson_user_id')->references('id')->on('users');
            $table->foreign('delivery_method_id')->references('id')->on('delivery_methods');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->foreign('tax_id')->references('id')->on('tax');
            $table->foreign('order_status_id')->references('id')->on('order_status');
            $table->foreign('payment_status_id')->references('id')->on('payment_status');
        });
    }
}
