<?php


namespace App\Repositories\Category;


use Illuminate\Http\Request;

interface ICategoryRepository
{
    public function getCategoryList(Request $request);

    public function storeCategory(Request $request);

    public function saveCategory($name, $description);

    public function saveCategoryVIImage($category_id, $image_id);

    public function updateCategory(Request $request, $id);

    public function updateCategoryWithImage($id, $image, $name, $description);

    public function deleteCategoryVIImage($category_id);

    public function getCategoryImage($category_id);

    public function updateCategoryVImage($category_id, $image_id);

    public function updateCategoryWithoutImage($id, $name, $description);

    public function getCategoryListWithDetails();

    public function getCategoryById($id);

    public function deleteCategory($category_id);

    public function destroyCategory($category_id);

    public function saveCategoryWithImage($name, $description, $imageId);

    public function getCategory($id);

    public function getCategories();
}
