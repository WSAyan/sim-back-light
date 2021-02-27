<?php


namespace App\Repositories\Product;


use Illuminate\Http\Request;

interface IProductRepository
{
    public function getProductList();

    public function storeProduct(Request $request);

    public function showProduct($id);

    public function getProductDetailsById($id);

    public function getProductById($id);

    public function getProductOptionsWithDetails($id);

    public function getProductOptionsWithProduct($id);

    public function generateSku($productName, $brandName, $categoryName);

    public function updateProductStock($product_id, $stock_id, $quantity);
}
