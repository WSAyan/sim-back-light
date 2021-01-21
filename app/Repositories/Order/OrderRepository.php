<?php


namespace App\Repositories\Order;


use App\Order;
use App\OrderVProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make($request->all(), [
            'salesperson_user_id' => 'required|numeric',
            'delivery_address' => 'required|string',
            'delivery_method_id' => 'required|numeric',
            'payment_method_id' => 'required|numeric',
            'tax_id' => 'required|numeric',
            'order_status_id' => 'required|numeric',
            'payment_status_id' => 'required|numeric',
            'total_price' => 'required',
            'total_payable' => 'required',
            'total_paid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product_list = json_decode($request->get('product_list'), true);

        // validation: empty product list
        if ($product_list == null || empty($product_list)) {
            return response()->json([
                'success' => false,
                'message' => 'No product selected'
            ], 400);
        }

        // find invalid products
        $isInStock = true;
        $isValidProduct = true;
        $outOfStockProductName = null;
        foreach ($product_list as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            $product = DB::table('products')->where('id', $product_id)->first();
            // check if product exists
            if ($product == null) {
                $isValidProduct = false;
                break;
            }

            // check stock
            if ($quantity > $product->stock_quantity) {
                $outOfStockProductName = $product->name;
                $isInStock = false;
                break;
            }
        }

        // validation: invalid product
        if ($isValidProduct == false) {
            return response()->json([
                'success' => false,
                'message' => "Invalid product found!"
            ], 400);
        }

        // validation: product out of stock
        if ($isInStock == false) {
            return response()->json([
                'success' => false,
                'message' => "Sorry! $outOfStockProductName is out of stock"
            ], 400);
        }

        $invoice_id = uniqid('order-');
        $salesperson_user_id = $request->get('salesperson_user_id');
        $customer_user_id = $request->get('customer_user_id');
        $deliveryman_user_id = $request->get('deliveryman_user_id');
        $customer_name = $request->get('customer_name');
        $customer_phone = $request->get('customer_phone');
        $customer_address = $request->get('customer_address');
        $delivery_address = $request->get('delivery_address');
        $delivery_location_lat = $request->get('delivery_location_lat');
        $delivery_location_long = $request->get('delivery_location_long');
        $delivery_method_id = $request->get('delivery_method_id');
        $payment_method_id = $request->get('payment_method_id');
        $tax_id = $request->get('tax_id');
        $order_status_id = $request->get('order_status_id');
        $payment_status_id = $request->get('payment_status_id');
        $total_price = $request->get('total_price');
        $total_payable = $request->get('total_payable');
        $total_paid = $request->get('total_paid');

        // insert orders in order table
        $order = new Order([
            'invoice_id' => $invoice_id,
            'salesperson_user_id' => $salesperson_user_id,
            'customer_user_id' => $customer_user_id,
            'deliveryman_user_id' => $deliveryman_user_id,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'customer_address' => $customer_address,
            'delivery_address' => $delivery_address,
            'delivery_location_lat' => $delivery_location_lat,
            'delivery_location_long' => $delivery_location_long,
            'delivery_method_id' => $delivery_method_id,
            'payment_method_id' => $payment_method_id,
            'tax_id' => $tax_id,
            'order_status_id' => $order_status_id,
            'payment_status_id' => $payment_status_id,
            'total_price' => $total_price,
            'total_payable' => $total_payable,
            'total_paid' => $total_paid,
        ]);
        $order->save();

        // insert ordered products in orders_v_products
        foreach ($product_list as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            $orderVProduct = new OrderVProduct([
                'order_id' => $order->id,
                'product_id' => $product_id,
                'order_quantity' => $quantity
            ]);
            $orderVProduct->save();

            // update product stock
            $product = DB::table('products')->where('id', $product_id)->first();
            $updatedQuantity = $product->stock_quantity - $quantity;
            $updatedProduct = DB::table('products')
                ->where('id', $product_id)
                ->update(['stock_quantity' => $updatedQuantity]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'ordered_products' => $product_list
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
