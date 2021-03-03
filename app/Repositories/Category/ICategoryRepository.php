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
}
