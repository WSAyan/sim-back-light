<?php
namespace App\Repositories\Counter;

use Illuminate\Http\Request;

interface ICounterRepository
{
    public function getInvoiceList();

    public function saveCounterEntry(Request $request);
}
