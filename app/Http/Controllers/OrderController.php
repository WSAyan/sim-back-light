<?php


namespace App\Http\Controllers;


use App\Repositories\Order\IOrderRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderRepo;

    public function __construct(IOrderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }

    public function index()
    {
        return $this->orderRepo->getOrdersList();
    }

    public function store(Request $request)
    {
        return $this->orderRepo->createOrder($request);
    }

    public function show($id)
    {
        return $this->orderRepo->showOrder($id);
    }
}
