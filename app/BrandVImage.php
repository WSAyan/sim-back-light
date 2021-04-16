<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandVImage extends Model
{
    protected $table = 'brands_v_images';

    protected $fillable = [
        'brand_id', 'image_id'
    ];
}
