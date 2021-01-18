<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'category_id', 'brand_id', 'unit_id', 'price', 'stock_quantity', 'name', 'description', 'sku'
    ];
}
