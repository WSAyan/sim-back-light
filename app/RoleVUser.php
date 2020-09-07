<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleVUser extends Model
{
    protected $table = 'roles_v_users';

    protected $fillable = [
        'role_id' , 'user_id'
    ];

    public function role()
    {
        return $this->hasOne('App\Role','id');
    }
}
