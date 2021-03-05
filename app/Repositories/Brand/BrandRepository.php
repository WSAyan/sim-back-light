<?php


namespace App\Repositories\Brand;

use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandRepository implements IBrandRepository
{
    public function showAllBrands()
    {
        return response()->json([
            'success' => true,
            'message' => 'Showing brands list',
            'brands' => $this->getAllBrands()
        ]);
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
