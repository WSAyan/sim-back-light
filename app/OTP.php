<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'otp';

    protected $fillable = [
        'to', 'otp', 'message_body', 'timeout', 'status'
    ];
}
