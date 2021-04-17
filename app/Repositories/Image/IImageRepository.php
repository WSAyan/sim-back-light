<?php


namespace App\Repositories\Image;


use Illuminate\Http\Request;

interface IImageRepository
{
    public function storeImage($postedImage);

    public function deleteImageFromStorage($fileName);

    public function deleteImageFromStorageById($imageId);

    public function writeInStorage($postedImage);

    public function updateImageFromStorageById($imageId, $postedImage);

    public function getImage($id);

    public function deleteImageById($imageId);

    public function storeImageRequest(Request $request);

    public function deleteImageRequest($id);

    public function updateImageRequest(Request $request, $id);

    public function getImageList(Request $request);

    public function deleteCategoryImage($imageId);

    public function deleteProductImage($imageId);

    public function deleteBrandImage($imageId);

    public function getImageInfoById($id);

    public function getRelationalImages($imageMap);
}
