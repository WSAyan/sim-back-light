<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator;

interface ICounterRepository
{
    public function getInvoiceList();

    public function saveCounterEntry(Request $request);
}
