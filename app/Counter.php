<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $table = 'counter';

    protected $fillable = [
        'invoice_id', 'user_id', 'type', 'customer_name', 'customer_address', 'customer_phone', 'amount', 'invoice_photo'
    ];
}
