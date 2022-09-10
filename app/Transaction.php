<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'from_account_no', 'to_account_no', 'amount', 'status'
    ];
}
