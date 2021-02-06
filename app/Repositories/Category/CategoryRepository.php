<?php


namespace App\Repositories\Category;


use App\Category;
use App\CategoryVImage;
use App\Image;
use App\Repositories\Category\ICategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = auth()->user();
        if ($request->get('user_id') != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $postedImage = $request->file('image');
        $imageName = uniqid('image-') . '-' . time() . '.' . $postedImage->getClientOriginalExtension();
        $postedImage->storeAs('images', $imageName);
        $image = new Image([
            'image' => $imageName
        ]);
        $status = $image->save();
        if ($status == false) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        $category = new Category([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);
        $status = $category->save();
        if ($status == false) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        $categoryVImage = new CategoryVImage([
            'category_id' => $category->id,
            'image_id' => $image->id,
        ]);
        $categoryVImage->save();

        return response()->json([
            'success' => true,
            'message' => 'Category successfully created'
        ], 201);
    }
}
