<?php


namespace App\Repositories\Order;


use Illuminate\Http\Request;

interface IOrderRepository
{
    public function getOrdersList();

    public function createOrder(Request $request);

    public function showOrder($id);
}
