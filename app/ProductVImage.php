<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductVImage extends Model
{
    protected $table = 'products_v_images';

    protected $fillable = [
        'product_id', 'image_id'
    ];
}
