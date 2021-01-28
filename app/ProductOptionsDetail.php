<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class ProductOptionsDetail extends Model
{
    protected $table = 'product_options_details';

    protected $fillable = [
        'product_options_id', 'name'
    ];
}
