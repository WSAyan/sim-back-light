<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'invoice_id',
        'salesperson_user_id',
        'customer_user_id',
        'deliveryman_user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'delivery_address',
        'delivery_location_lat',
        'delivery_location_long',
        'delivery_method_id',
        'payment_method_id',
        'tax_id',
        'order_status_id',
        'payment_status_id',
        'total_price',
        'total_payable',
        'total_paid'
    ];
}
