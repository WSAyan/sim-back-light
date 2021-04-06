<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmenuTree extends Model
{
    protected $table = 'submenu_tree';

    protected $fillable = [
        'submenu_screen_id', 'parent_screen_id'
    ];
}
