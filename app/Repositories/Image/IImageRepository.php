<?php


namespace App\Repositories\Image;


interface IImageRepository
{
    public function storeImage($postedImage);

    public function deleteImageFromStorage($fileName);

    public function deleteImageFromStorageById($imageId);

    public function writeInStorage($postedImage);

    public function updateImageFromStorageById($imageId, $postedImage);

    public function getImage($id);

    public function deleteImageById($imageId);
}
