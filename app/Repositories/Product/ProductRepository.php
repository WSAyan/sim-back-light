<?php


namespace App\Repositories\Product;


use App\Product;
use App\ProductVOption;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductRepository implements IProductRepository
{

    public function getProductList()
    {
        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->select(
                'products.id as product_id',
                'products.sku as product_sku',
                'products.name as product_name',
                'products.description as product_description',
                'products.price as product_unit_price',
                'products.stock_quantity as product_stock_quantity',
                'categories.name as category_name',
                'brands.brand_name as brand',
                'units.unit_name as unit',
                'units.is_reminder_allowed as unit_reminder_allowed'
            )
            ->orderBy('products.id', 'desc')
            ->paginate(25);

        return response()->json([
            'success' => true,
            'message' => 'Product list generated',
            'products' => $products
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'category_id' => 'required',
            'brand_id' => 'required',
            'unit_id' => 'required',
            'price' => 'required',
            'has_options' => 'required',
            'stock_quantity' => 'required',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        if ($request->get('user_id') != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $category_id = $request->get('category_id');
        $brand_id = $request->get('brand_id');
        $unit_id = $request->get('unit_id');
        $price = $request->get('price');
        $name = $request->get('name');
        $description = $request->get('description');
        $has_options = $request->get('has_options');
        $product_details = json_decode($request->get('product_details'), true);
        $stock_quantity = $request->get('stock_quantity');
        $sku = uniqid('sku-');

        $product = new Product([
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'unit_id' => $unit_id,
            'price' => $price,
            'name' => $name,
            'description' => $description,
            'has_options' => $has_options,
            'stock_quantity' => $stock_quantity
        ]);
        $product->save();

        if ($has_options == false) {
            // saving only one sku
            $stock = new Stock([
                'product_id' => $product->id,
                'sku' => $sku,
                'quantity' => $stock_quantity
            ]);
            $stock->save();
        } else {
            // saving multiple sku depending on options
            foreach ($product_details as $item) {
                $product_options_id = $item['product_options_id'];
                $product_options_details_id = $item['product_options_details_id'];
                $quantity = $item['quantity'];

                $stock = new Stock([
                    'product_id' => $product->id,
                    'sku' => $sku,
                    'quantity' => $quantity
                ]);
                $stock->save();

                $productVOption = new ProductVOption([
                    'product_id' => $product->id,
                    'product_options_id' => $product_options_id,
                    'product_options_details_id' => $product_options_details_id,
                    'stock_id' => $stock->id
                ]);
                $productVOption->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product successfully created'
        ], 201);
    }

    public function showProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Product showed',
            'product' => $product
        ]);
    }
}
