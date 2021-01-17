<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryVImage extends Model
{
    protected $table = 'categories_v_images';

    protected $fillable = [
        'category_id', 'image_id'
    ];
}
