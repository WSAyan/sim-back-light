<?php


namespace App\Repositories;


use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductRepository implements IProductRepository
{

    public function getProductList()
    {
        $products = DB::table('products')->paginate(25);
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

        $product = new Product([
            'category_id' => $request->get('category_id'),
            'brand_id' => $request->get('brand_id'),
            'unit_id' => $request->get('unit_id'),
            'price' => $request->get('price'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'sku' => uniqid('sku-')
        ]);

        $product = $product->save();

        if ($product == false) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product successfully created'
        ], 201);
    }
}
