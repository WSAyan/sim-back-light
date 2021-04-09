<?php


namespace App\Repositories\Image;


use App\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageRepository implements IImageRepository
{
    public function getImage($id)
    {
        return DB::table('images')
            ->where('images.id', $id)
            ->first();
    }

    public function storeImage($postedImage)
    {
        $imageName = $this->writeInStorage($postedImage);

        $image = new Image([
            'image' => $imageName
        ]);
        $status = $image->save();

        if ($status == false) {
            return null;
        }

        return $image;
    }

    public function writeInStorage($postedImage)
    {
        $imageName = uniqid('image-') . '-' . time() . '.' . $postedImage->getClientOriginalExtension();
        $postedImage->storeAs('images', $imageName);

        return $imageName;
    }

    public function deleteImageFromStorage($fileName)
    {
        return Storage::delete("images/${$fileName}");
    }

    public function deleteImageFromStorageById($imageId)
    {
        $image = $this->getImage($imageId);

        if (is_null($image)) return false;

        $fileName = $image->image;

        return Storage::delete("images/".$fileName);
    }

    public function updateImageFromStorageById($imageId, $postedImage)
    {
        $status = $this->deleteImageFromStorageById($imageId);

        if ($status == false) return null;

        $imageName = $this->writeInStorage($postedImage);

        DB::table('images')
            ->where('images.id', $imageId)
            ->update(
                [
                    'image' => $imageName
                ]
            );

        return $this->getImage($imageId);
    }
}
