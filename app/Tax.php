<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tax';

    protected $fillable = [
        'tax_method',
        'percentage',
        'tax_invoice_number'
    ];
}
