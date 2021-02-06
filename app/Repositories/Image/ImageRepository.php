<?php


namespace App\Repositories\Image;


use App\Image;

class ImageRepository implements IImageRepository
{
    public function storeImage($postedImage)
    {
        $imageName = uniqid('image-') . '-' . time() . '.' . $postedImage->getClientOriginalExtension();
        $postedImage->storeAs('images', $imageName);
        $image = new Image([
            'image' => $imageName
        ]);
        $status = $image->save();

        if ($status == false) {
            return null;
        }

        return $image;
    }
}
