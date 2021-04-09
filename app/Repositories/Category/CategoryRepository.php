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
        $imageUrl = asset('images') . '/';

        $categories = DB::table('categories')
            ->leftJoin('categories_v_images', 'categories.id', '=', 'categories_v_images.category_id')
            ->leftJoin('images', 'categories_v_images.image_id', '=', 'images.id')
            ->selectRaw(
                "categories.id as id,
                categories.name as name,
                categories.description as description,
                CONCAT('$imageUrl' , images.image) as image_url"
            )
            ->orderBy('categories.id')
            ->paginate(25);

        return response()->json([
            'success' => true,
            'message' => 'Category list generated',
            'categories' => $categories
        ]);
    }

    public function getCategoryListWithDetails()
    {
        return DB::table('categories')
            ->leftJoin('categories_v_images', 'categories.id', '=', 'categories_v_images.category_id')
            ->leftJoin('images', 'categories_v_images.image_id', '=', 'images.id')
            ->select
            (
                'categories.id as id',
                'categories.name as name',
                'categories.description as description',
                'images.id as image_id',
                'images.image as image'
            )
            ->get();
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
        $category = DB::table('categories')
            ->where('categories.id', $id)
            ->first();

        $categoryVImage = $this->getCategoryImage($id);
        if (is_null($categoryVImage)) return null;

        $image = $this->imageRepo->getImage($categoryVImage->image_id);

        $categoryDetails = [];
        $categoryDetails['id'] = $category->id;
        $categoryDetails['name'] = $category->name;
        $categoryDetails['description'] = $category->description;
        $categoryDetails['image']['id'] = $image->id;
        $categoryDetails['image']['name'] = $image->image;
        $categoryDetails['image']['image_url'] = asset('images/' . $image->image);

        return $categoryDetails;
    }

    public function getCategoryById($id)
    {
        $imageUrl = asset('images') . '/';

        return DB::table('categories')
            ->leftJoin('categories_v_images', 'categories.id', '=', 'categories_v_images.category_id')
            ->leftJoin('images', 'categories_v_images.image_id', '=', 'images.id')
            ->selectRaw
            (
                "
                categories.id as id,
                categories.name as name,
                categories.description as description,
                CONCAT('$imageUrl' , images.image) as image_url
                "
            )
            ->where('categories.id', '=', $id)
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

    public function updateCategory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();
        if ($request->get('user_id') != $user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $name = $request->get('name');
        $description = $request->get('description');
        $image = $request->file('image');

        $category = null;
        if (is_null($image) || empty($image)) {
            $category = $this->updateCategoryWithoutImage($id, $name, $description);
        } else {
            $category = $this->updateCategoryWithImage($id, $image, $name, $description);
        }

        if (is_null($category)) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category successfully updated',
            'category' => $category
        ], 200);
    }

    public function updateCategoryWithImage($id, $image, $name, $description)
    {
        $imageData = $this->getCategoryImage($id);

        $imageData = $this->imageRepo->updateImageFromStorageById($imageData->id, $image);

        if (is_null($imageData)) return null;

        DB::table('categories')
            ->where('categories.id', $id)
            ->update(
                [
                    'name' => $name,
                    'description' => $description
                ]
            );

        return $this->getCategoryById($id);
    }

    public function updateCategoryWithoutImage($id, $name, $description)
    {
        DB::table('categories')
            ->where('categories.id', $id)
            ->update(
                [
                    'name' => $name,
                    'description' => $description
                ]
            );

        return $this->getCategoryById($id);
    }

    public function deleteCategoryVIImage($category_id, $image_id)
    {
        return DB::table('categories_v_images')
            ->where('categories_v_images.category_id', $category_id)
            ->where('categories_v_images.image_id', $image_id)
            ->delete();
    }

    public function getCategoryImage($category_id)
    {
        return DB::table('categories_v_images')
            ->where('categories_v_images.category_id', $category_id)
            ->first();
    }

    public function updateCategoryVImage($category_id, $image_id)
    {
        DB::table('categories_v_images')
            ->where('categories_v_images.category_id', $category_id)
            ->update(
                [
                    'category_id' => $category_id,
                    'image_id' => $image_id
                ]
            );

        return $this->getCategoryImage($category_id);
    }

    public function deleteCategory($category_id)
    {
        $categoryImage = $this->getCategoryImage($category_id);

        $status = $this->deleteCategoryVIImage($category_id, $categoryImage->id);

        if ($status == false) return false;

        $status = $this->imageRepo->deleteImageById($categoryImage->id);

        if ($status == false) return false;

        return DB::table('categories')
            ->where('categories.id', $category_id)
            ->delete();
    }

    public function destroyCategory($category_id)
    {
        $status = $this->deleteCategory($category_id);

        if ($status == false) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category successfully deleted'
        ], 201);
    }
}
