<?php


namespace App\Repositories\Order;


use App\Order;
use App\OrderVProduct;
use App\Repositories\Product\IProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderRepository implements IOrderRepository
{
    private $productRepo;

    public function __construct(IProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function getOrdersList()
    {
        $orders = DB::table('orders')->paginate(25);

        return response()->json([
            'success' => true,
            'message' => 'Order list test',
            'orders' => $orders
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

            $product = $this->productRepo->getProductById($product_id);
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

        $invoice_id = strtoupper(uniqid('#'));
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
            $stock_id = $item['stock_id'];
            $quantity = $item['quantity'];

            $orderVProduct = new OrderVProduct([
                'order_id' => $order->id,
                'product_id' => $product_id,
                'stock_id' => $stock_id,
                'order_quantity' => $quantity
            ]);
            $orderVProduct->save();

            // update product stock
            $this->productRepo->updateProductStock($product_id, $stock_id, $quantity);
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
            'message' => 'Showing order details',
            'order' => $this->getOrderDetailsById($id)
        ]);
    }

    public function getOrderDetailsById($id)
    {
        $order = DB::table('orders')
            ->join('orders_v_products', 'orders.id', '=', 'orders_v_products.order_id')
            ->join('products', 'orders_v_products.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->join('stocks', 'orders_v_products.stock_id', '=', 'stocks.id')
            ->join('products_v_options', 'orders_v_products.stock_id', '=', 'products_v_options.stock_id')
            ->join('product_options', 'products_v_options.product_options_id', '=', 'product_options.id')
            ->join('product_options_details', 'products_v_options.product_options_details_id', '=', 'product_options_details.id')
            ->selectRaw(
                "
                            orders.id as id,
                            orders.invoice_id as invoice_id,
                            orders.customer_name as customer_name,
                            orders.customer_phone as customer_phone,
                            orders.customer_address as customer_address,
                            orders.delivery_address as delivery_address,
                            orders.total_price as total_price,
                            orders.total_payable as total_payable,
                            orders.total_paid as total_paid,
                            products.id as product_id,
                            products.name as product_name,
                            products.description as product_description,
                            products.price as product_unit_price,
                            products.stock_quantity as product_stock_quantity,
                            categories.name as category_name,
                            brands.brand_name as brand_name,
                            units.unit_name as unit,
                            units.is_reminder_allowed as unit_reminder_allowed,
                            orders_v_products.order_quantity as order_quantity,
                            stocks.quantity as current_stock_quantity,
                            product_options.name as product_options_name,
                            product_options_details.name as product_options_details
                "
            )
            ->where('orders.id', $id)
            ->get();

        $result = [];
        $i = 0;
        foreach ($order as $item) {
            $result['id'] = $item->id;
            $result['invoice_id'] = $item->invoice_id;
            $result['customer_name'] = $item->customer_name;
            $result['customer_phone'] = $item->customer_phone;
            $result['customer_address'] = $item->customer_address;
            $result['delivery_address'] = $item->delivery_address;
            $result['total_price'] = $item->total_price;
            $result['total_payable'] = $item->total_payable;
            $result['total_paid'] = $item->total_paid;

            $result['ordered_products'][$i]['product_id'] = $item->product_id;
            $result['ordered_products'][$i]['product_name'] = $item->product_name;
            $result['ordered_products'][$i]['product_description'] = $item->product_description;
            $result['ordered_products'][$i]['product_unit_price'] = $item->product_unit_price;
            $result['ordered_products'][$i]['category_name'] = $item->category_name;
            $result['ordered_products'][$i]['brand_name'] = $item->brand_name;
            $result['ordered_products'][$i]['unit'] = $item->unit;
            $result['ordered_products'][$i]['unit_reminder_allowed'] = $item->unit_reminder_allowed;
            $result['ordered_products'][$i]['order_quantity'] = $item->order_quantity;
            $result['ordered_products'][$i]['current_stock_quantity'] = $item->current_stock_quantity;
            $result['ordered_products'][$i]['product_options_name'] = $item->product_options_name;
            $result['ordered_products'][$i]['product_options_details'] = $item->product_options_details;

            $i++;
        }

        return $result;
    }
}
