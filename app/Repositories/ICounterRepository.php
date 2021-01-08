<?php
namespace App\Repositories;

use Illuminate\Http\Request;
use Validator;
use App\Counter;
use Illuminate\Support\Facades\Storage;

interface ICounterRepository
{
    public function getInvoiceList();

    public function saveCounterEntry(Request $request);


}
