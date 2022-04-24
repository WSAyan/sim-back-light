<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = 'collections';

    protected $fillable = [
        'invoice_id', 'collector_user_id', 'retailer_user_id', 'comments', 'amount'
    ];
}
