<?php


namespace App\Repositories\Brand;

use Illuminate\Support\Facades\DB;

class BrandRepository implements IBrandRepository
{
    public function getAllBrands()
    {

    }

    public function getBrandDetailsById($id)
    {
        return DB::table('brands')
            ->where('brands.id', $id)
            ->first();
    }
}
