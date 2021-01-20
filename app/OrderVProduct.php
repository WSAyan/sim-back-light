<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class OrderVProduct extends Model
{
    protected $table = 'orders_v_products';

    protected $fillable = [
        'order_id',
        'product_id',
        'order_quantity'
    ];

}
