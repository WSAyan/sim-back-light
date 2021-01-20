<?php


namespace App\Repositories\Product;


use Illuminate\Http\Request;

interface IProductRepository
{
    public function getProductList();

    public function storeProduct(Request $request);

    public function showProduct($id);
}
