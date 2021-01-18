<?php


namespace App\Repositories;


use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryRepository implements ICategoryRepository
{

    public function getCategoryList()
    {
        $categories = DB::table('categories')->paginate(25);
        return response()->json([
            'success' => true,
            'message' => 'Category list generated',
            'categories' => $categories
        ]);
    }

    public function storeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        if ($request->get('user_id') != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
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
