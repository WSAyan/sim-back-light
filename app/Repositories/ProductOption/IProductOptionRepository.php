<?php


namespace App\Repositories\ProductOption;


use Illuminate\Http\Request;

interface IProductOptionRepository
{
    public function showProductOptions($request);

    public function storeProductOption($request);

    public function showProductOption($id);

    public function getProductOptionsWithDetails($id);

    public function getProductOptionsWithProduct($id);

    public function getProductOptions();

    public function updatePrductOption(Request $request, $id);

    public function destroyProductOption($id);
}
