<?php

namespace App\Http\Controllers;

use App\Repositories\Counter\ICounterRepository;
use Illuminate\Http\Request;

class CounterController extends Controller
{
    private $counterRepo;

    public function __construct(ICounterRepository $counterRepo)
    {
        $this->counterRepo = $counterRepo;
    }

    public function index()
    {
        return $this->counterRepo->getInvoiceList();
    }

    public function store(Request $request)
    {
        return $this->counterRepo->saveCounterEntry($request);
    }
}
