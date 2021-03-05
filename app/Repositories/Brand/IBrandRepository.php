<?php


namespace App\Repositories\Brand;


use Illuminate\Http\Request;

interface IBrandRepository
{
    public function showAllBrands();

    public function showBrandDetails($id);

    public function getAllBrands();

    public function getBrandDetailsById($id);

    public function storeBrand(Request $request);

    public function saveBrand($brand_name);
}

