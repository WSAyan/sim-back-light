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

    public function saveProduct($category_id, $brand_id, $unit_id, $price, $name, $description, $has_options, $stock_quantity);

    public function saveStock($product_id, $sku, $stock_quantity);

    public function saveProductVOption($product_id, $product_options_id, $product_options_details_id, $stock_id);

    public function saveProductVImage($product_id, $image_id);

    public function saveProductImages($images, $product_id);

    public function getProductImages($product_id);
}
