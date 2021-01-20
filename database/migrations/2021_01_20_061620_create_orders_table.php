<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id');
            $table->unsignedBigInteger('salesperson_user_id');
            $table->unsignedBigInteger('customer_user_id')->nullable();
            $table->unsignedBigInteger('deliveryman_user_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('delivery_address');
            $table->decimal('delivery_location_lat', 9, 6)->nullable();
            $table->decimal('delivery_location_long', 9, 6)->nullable();
            $table->unsignedBigInteger('delivery_method_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('tax_id');
            $table->unsignedBigInteger('order_status_id');
            $table->unsignedBigInteger('payment_status_id');
            $table->unsignedDecimal('total_price', 8, 2);
            $table->unsignedDecimal('total_payable', 8, 2);
            $table->unsignedDecimal('total_paid', 8, 2);
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
        Schema::dropIfExists('orders');
    }
}
