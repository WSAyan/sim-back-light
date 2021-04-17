<?php


namespace App\Repositories\Brand;

use App\Brand;
use App\BrandVImage;
use App\Repositories\Image\IImageRepository;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandRepository implements IBrandRepository
{
    private $imageRepo;

    public function __construct(IImageRepository $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

    public function showAllBrands(Request $request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $brands = DB::table('brands')
            ->where('brands.brand_name', 'LIKE', "%{$query}%")
            ->orderBy('brands.id')
            ->paginate($size)
            ->toArray();

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Brands list generated', $this->formatBrands($brands), 'brands', true);
    }

    public function showBrandDetails($id)
    {
        $brand = $this->getBrandDetailsById($id);

        if (is_null($brand)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, 'Brand not found', null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Showing brand details', $brand, 'brand', true);
    }

    public function saveBrandWithImage($name, $imageId)
    {
        $brand = $this->saveBrand($name);

        $this->saveBrandVIImage($brand->id, $imageId);

        return $this->getBrandDetailsById($brand->id);
    }

    public function saveBrandVIImage($brand_id, $image_id)
    {
        $brandVImage = new BrandVImage([
            'brand_id' => $brand_id,
            'image_id' => $image_id,
        ]);
        $brandVImage->save();

        return $brandVImage;
    }

    public function getAllBrands()
    {
        return DB::table('brands')
            ->get();
    }

    /**
     * format brand category item
     * @param $brand
     * @return array
     */
    private function formatBrand($brand)
    {
        $brandDetails = [];
        $brandDetails['id'] = $brand->id;
        $brandDetails['name'] = $brand->brand_name;
        $brandDetails['images'] = $this->getBrandImage($brand->id);

        return $brandDetails;
    }

    /**
     * formats brand list for response
     * @param $brands
     * @return mixed
     */
    private function formatBrands($brands)
    {
        $data = $brands['data'];
        $i = 0;
        foreach ($data as $item) {
            $brands['data'][$i] = $this->formatBrand($item);
            $i++;
        }

        return $brands;
    }

    public function getBrandImage($brand_id)
    {
        $imageMap = DB::table('brands_v_images')
            ->where('brands_v_images.brand_id', $brand_id)
            ->get();

        return $this->imageRepo->getRelationalImages($imageMap);
    }

    public function getBrandDetailsById($id)
    {
        $brand = $this->getBrand($id);

        if (is_null($brand)) return null;

        return $this->formatBrand($brand);
    }

    public function getBrand($id)
    {
        return DB::table('brands')
            ->where('brands.id', $id)
            ->first();
    }

    public function storeBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_name' => 'required|string|min:3',
            'image_id' => 'required'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, $validator->errors()->all());
        }

        $brand_name = $request->get('brand_name');
        $imageId = $request->get('image_id');

        $brand = $this->saveBrandWithImage($brand_name, $imageId);

        if (is_null($brand)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_CREATE, 'Brand successfully created', $brand, 'brand', true);
    }

    public function updateBrand(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, 'Validation failed', $validator->errors()->all());
        }

        $name = $request->get('name');
        $imageId = $request->get('image_id');

        $brand = null;
        if (is_null($imageId) || empty($imageId)) {
            $brand = $this->updateBrandWithoutImage($id, $name);
        } else {
            $brand = $this->updateBrandWithImage($id, $imageId, $name);
        }

        if (is_null($brand)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Brand successfully updated', $brand, 'brand', true);
    }

    public function saveBrand($brand_name)
    {
        $brand = new Brand([
            'brand_name' => $brand_name
        ]);

        $brand->save();

        return $brand;
    }

    public function updateBrandWithImage($id, $imageId, $name)
    {
        $this->updateBrandVImage($id, $imageId);

        DB::table('brands')
            ->where('brands.id', $id)
            ->update(
                [
                    'brand_name' => $name
                ]
            );

        return $this->getBrandDetailsById($id);
    }

    public function updateBrandWithoutImage($id, $name)
    {
        DB::table('brands')
            ->where('brands.id', $id)
            ->update(
                [
                    'brand_name' => $name
                ]
            );

        return $this->getBrandDetailsById($id);
    }

    public function updateBrandVImage($brand_id, $image_id)
    {
        return DB::table('brands_v_images')
            ->where('brands_v_images.brand_id', $brand_id)
            ->update(
                [
                    'brand_id' => $brand_id,
                    'image_id' => $image_id
                ]
            );
    }

    public function deleteBrandVIImage($brand_id)
    {
        return DB::table('brands_v_images')
            ->where('brands_v_images.brand_id', $brand_id)
            ->delete();
    }

    public function deleteBrand($brand_id)
    {
        $this->deleteBrandVIImage($brand_id);

        return DB::table('brands')
            ->where('brands.id', $brand_id)
            ->delete();
    }

    public function destroyCategory($brand_id)
    {
        $status = $this->deleteBrand($brand_id);

        if ($status == false) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, "Unknown brand", null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Brand successfully deleted', null, null, false);
    }
}
