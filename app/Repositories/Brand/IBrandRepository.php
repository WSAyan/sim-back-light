<?php


namespace App\Repositories\Brand;


interface IBrandRepository
{
    public function getAllBrands();

    public function getBrandDetailsById($id);
}
