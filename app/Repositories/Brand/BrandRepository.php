<?php


namespace App\Repositories\Brand;

use App\Brand;
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
        return response()->json([
            'success' => true,
            'message' => 'Showing brand details',
            'brand' => $this->getBrandDetailsById($id)
        ]);
    }

    public function getAllBrands()
    {
        return DB::table('brands')
            ->get();
    }

    /**
     * format single category item
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
     * formats category list for response
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
        $brandVImage = DB::table('brands_v_images')
            ->where('brands_v_images.brand_id', $brand_id)
            ->first();

        if (is_null($brandVImage)) return [];

        return $this->imageRepo->getAllImagesById($brandVImage->image_id);
    }

    public function getBrandDetailsById($id)
    {
        return DB::table('brands')
            ->where('brands.id', $id)
            ->first();
    }

    public function storeBrand(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'brand_name' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        if ($request->get('user_id') != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $brand_name = $request->get('brand_name');

        $brand = $this->saveBrand($brand_name);

        return response()->json([
            'success' => true,
            'message' => 'Brand successfully created',
            'brand' => $brand
        ], 201);
    }

    public function saveBrand($brand_name)
    {
        $brand = new Brand([
            'brand_name' => $brand_name
        ]);

        $brand->save();

        return $brand;
    }
}
