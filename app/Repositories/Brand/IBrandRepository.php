<?php


namespace App\Repositories\Brand;


use Illuminate\Http\Request;

interface IBrandRepository
{
    public function showAllBrands(Request $request);

    public function showBrandDetails($id);

    public function getAllBrands();

    public function getBrandDetailsById($id);

    public function storeBrand(Request $request);

    public function saveBrand($brand_name);

    public function getBrandImage($brand_id);

    public function saveBrandVIImage($brand_id, $image_id);

    public function saveBrandWithImage($name, $imageId);

    public function updateBrandWithImage($id, $imageId, $name);

    public function updateBrandWithoutImage($id, $name);

    public function updateBrand(Request $request, $id);
}

