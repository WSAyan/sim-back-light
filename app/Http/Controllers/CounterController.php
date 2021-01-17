<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ICounterRepository;

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
