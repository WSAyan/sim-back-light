<?php


namespace App\Repositories\Order;


use Illuminate\Http\Request;

class OrderRepository implements IOrderRepository
{

    public function getOrdersList()
    {
        return response()->json([
            'success' => true,
            'message' => 'Order list test',
            'orders' => null
        ]);
    }

    public function createOrder(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Order create test'
        ], 201);
    }

    public function showOrder($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Order show test',
            'order' => null
        ]);
    }
}
