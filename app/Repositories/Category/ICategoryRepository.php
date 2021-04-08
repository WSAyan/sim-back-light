<?php


namespace App\Repositories\Category;


use Illuminate\Http\Request;

interface ICategoryRepository
{
    public function getCategoryList();

    public function storeCategory(Request $request);

    public function getCategoryDetailsById($id);

    public function saveCategory($name, $description);

    public function saveCategoryVIImage($category_id, $image_id);

    public function updateCategory(Request $request, $id);

    public function updateCategoryDetails($id, $image, $name, $description);

    public function deleteCategoryVIImage($category_id, $image_id);

    public function getCategoryImage($category_id);

    public function updateCategoryVImage($category_id, $image_id);
}
