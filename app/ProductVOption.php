<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class ProductVOption extends Model
{
    protected $table = 'products_v_options';

    protected $fillable = [
        'product_id', 'product_options_id', 'product_options_details_id', 'stock_id'
    ];
}
