<?php

namespace App\Http\Controllers;

use App\Repositories\ProductOption\IProductOptionRepository;
use Illuminate\Http\Request;

class ProductOptionController extends Controller
{
    private $productOptionRepo;

    public function __construct(IProductOptionRepository $productOptionRepo)
    {
        $this->productOptionRepo = $productOptionRepo;
    }

    public function showProductOptionList(Request $request)
    {
        return $this->productOptionRepo->showProductOptions($request);
    }

    public function store(Request $request)
    {
        return $this->productOptionRepo->storeProductOption($request);
    }

    public function show($id)
    {
        return $this->productOptionRepo->showProductOption($id);
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
