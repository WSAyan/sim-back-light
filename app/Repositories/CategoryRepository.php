<?php


namespace App\Repositories;


use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryRepository implements ICategoryRepository
{

    public function getCategoryList()
    {

    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $category = new Category([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);

        $category = $category->save();

        if ($category == false) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category successfully created'
        ], 201);
    }
}
