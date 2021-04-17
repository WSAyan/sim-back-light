<?php


namespace App\Repositories\Image;


use App\Image;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageRepository implements IImageRepository
{
    public function storeImageRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, 'Validation failed', $validator->errors()->all());
        }

        $postedImage = $request->file('image');

        $image = $this->storeImage($postedImage);

        return ResponseFormatter::successResponse(SUCCESS_TYPE_CREATE, "Upload successful", $image, "image", true);
    }

    public function deleteImageRequest($id)
    {
        $status = $this->deleteImageById($id);

        if ($status == false) return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, "Image deleted", null, null, false);
    }

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

        return $this->formatImage($image);
    }

    private function formatImage($image)
    {
        $result = [];
        $result['id'] = $image->id;
        $result['name'] = $image->image;
        $result['url'] = asset('images') . '/' . $image->image;

        return $result;
    }

    private function formatImages($images)
    {
        $data = $images['data'];
        $i = 0;
        foreach ($data as $item) {
            $images['data'][$i] = $this->formatImage($item);
            $i++;
        }

        return $images;
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

        return Storage::delete("images/" . $fileName);
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

    public function deleteImageById($imageId)
    {
        $this->deleteCategoryImage($imageId);
        $this->deleteBrandImage($imageId);
        $this->deleteProductImage($imageId);

        $status = $this->deleteImageFromStorageById($imageId);

        if ($status == false) return false;

        return DB::table('images')
            ->where('images.id', $imageId)
            ->delete();
    }

    public function updateImageRequest(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, 'Validation failed', $validator->errors()->all());
        }

        $postedImage = $request->file('image');

        $image = $this->updateImageFromStorageById($id, $postedImage);

        if (is_null($image)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_CREATE, "Image updated", $this->formatImage($image), "image", true);
    }

    public function getImageList(Request $request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $images = DB::table('images')
            ->where('images.image', 'LIKE', "%{$query}%")
            ->orderBy('images.id')
            ->paginate($size)
            ->toArray();

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Image list generated', $this->formatImages($images), "images", true);
    }

    public function getImageInfoById($id)
    {
        $image = $this->getImage($id);

        if (is_null($image)) return null;

        return $this->formatImage($image);
    }

    public function getRelationalImages($imageMap)
    {
        if (is_null($imageMap)) return [];

        $images = [];
        $i = 0;
        foreach ($imageMap as $item) {
            $images[$i] = $this->getImageInfoById($item->image_id);
            $i++;
        }
        return $images;
    }

    public function deleteCategoryImage($imageId)
    {
        return DB::table('categories_v_images')
            ->where('categories_v_images.image_id', $imageId)
            ->delete();
    }

    public function deleteProductImage($imageId)
    {
        return DB::table('products_v_images')
            ->where('products_v_images.image_id', $imageId)
            ->delete();
    }

    public function deleteBrandImage($imageId)
    {
        return DB::table('brands_v_images')
            ->where('brands_v_images.image_id', $imageId)
            ->delete();
    }
}
