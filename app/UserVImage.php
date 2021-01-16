<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserVImage extends Model
{
    protected $table = 'users_v_images';

    protected $fillable = [
        'user_id', 'image_id'
    ];
}
