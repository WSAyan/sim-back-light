<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'otp';

    protected $fillable = [
        'to_user_id', 'phone_number', 'otp', 'message_body', 'timeout', 'status'
    ];
}
