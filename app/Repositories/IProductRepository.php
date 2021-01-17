<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface IProductRepository
{
    public function getProductList();

    public function storeProduct(Request $request);
}
