<?php


namespace App\Repositories;


use Illuminate\Http\Request;

interface ICategoryRepository
{
    public function getCategoryList();

    public function storeCategory(Request $request);
}
