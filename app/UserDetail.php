<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $table = 'user_details';

    protected $fillable = [
        'user_id', 'location_id', 'full_name', 'phone', 'address', 'track_id', 'active_status', 'description'
    ];
}
