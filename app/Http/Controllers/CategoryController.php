<?php

namespace App\Http\Controllers;

use App\Repositories\Category\ICategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryRepo;

    public function __construct(ICategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function showCategoryList(Request $request)
    {
        return $this->categoryRepo->getCategoryList($request);
    }

    public function store(Request $request)
    {
        return $this->categoryRepo->storeCategory($request);
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
        return $this->categoryRepo->updateCategory($request, $id);
    }


    public function destroy($id)
    {
        return $this->categoryRepo->destroyCategory($id);
    }
}
