<?php

namespace App\Http\Controllers;

use App\Repositories\Product\IProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productRepo;

    public function __construct(IProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function showProductList(Request $request)
    {
        return $this->productRepo->showProductList($request);
    }

    public function store(Request $request)
    {
        return $this->productRepo->storeProduct($request);
    }

    public function show($id)
    {
        return $this->productRepo->showProduct($id);
    }

    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
        return $this->productRepo->updateProduct($request, $id);
    }


    public function destroy($id)
    {

    }
}
