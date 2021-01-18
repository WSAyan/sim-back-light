<?php

namespace App\Http\Controllers;

use App\Repositories\IProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productRepo;

    public function __construct(IProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function index()
    {
        return $this->productRepo->getProductList();
    }

    public function store(Request $request)
    {
        return $this->productRepo->storeProduct($request);
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {

    }


    public function destroy($id)
    {

    }
}
