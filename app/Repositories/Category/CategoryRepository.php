<?php


namespace App\Repositories\Category;


use App\Category;
use App\CategoryVImage;
use App\Repositories\Image\IImageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryRepository implements ICategoryRepository
{
    private $imageRepo;

    public function __construct(IImageRepository $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

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
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();
        if ($request->get('user_id') != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $image = $this->imageRepo->storeImage($request->file('image'));
        if (is_null($image)) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        $category = $this->saveCategory($request->get('name'), $request->get('description'));

        $categoryVImage = $this->saveCategoryVIImage($category->id, $image->id);

        return response()->json([
            'success' => true,
            'message' => 'Category successfully created'
        ], 201);
    }

    public function getCategoryDetailsById($id)
    {
        return DB::table('categories')
            ->where('categories.id', $id)
            ->first();
    }

    public function saveCategory($name, $description)
    {
        $category = new Category([
            'name' => $name,
            'description' => $description,
        ]);
       $category->save();

        return $category;
    }

    public function saveCategoryVIImage($category_id, $image_id)
    {
        $categoryVImage = new CategoryVImage([
            'category_id' => $category_id,
            'image_id' => $image_id,
        ]);
        $categoryVImage->save();

        return $categoryVImage;
    }
}
