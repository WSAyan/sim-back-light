<?php


namespace App\Repositories\Category;


use App\Category;
use App\CategoryVImage;
use App\Repositories\Image\IImageRepository;
use App\Utils\RequestFormatter;
use App\Utils\ResponseFormatter;
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

    public function getCategoryList(Request $request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $categories = DB::table('categories')
            ->selectRaw(
                "categories.id as id,
                categories.name as name,
                categories.description as description"
            )
            ->where('categories.name', 'LIKE', "%{$query}%")
            ->orderBy('categories.id')
            ->paginate($size)
            ->toArray();

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Category list generated', $this->formatCategories($categories), 'categories', true);
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
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, 'Validation failed', $validator->errors()->all());
        }

        $name = $request->get('name');
        $description = $request->get('description');
        $images = RequestFormatter::resolveJsonConfusion($request->get('images'));

        $category = null;
        if (is_null($images) || empty($images)) {
            $category = $this->saveCategoryWithoutImage($name, $description);
        } else {
            if (sizeof($images) > 1) {
                return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, ["You can upload maximum 1 image"]);
            }

            $category = $this->saveCategoryWithImage($name, $description, $images);
        }


        if (is_null($category)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_CREATE, 'Category successfully created', $category, 'category', true);
    }

    /**
     * format single category item
     * @param $category
     * @return array
     */
    private function formatCategory($category)
    {
        $categoryDetails = [];
        $categoryDetails['id'] = $category->id;
        $categoryDetails['name'] = $category->name;
        $categoryDetails['description'] = $category->description;
        $categoryDetails['images'] = $this->getCategoryImage($category->id);

        return $categoryDetails;
    }

    /**
     * formats category list for response
     * @param $categories
     * @return mixed
     */
    private function formatCategories($categories)
    {
        $data = $categories['data'];
        $i = 0;
        foreach ($data as $item) {
            $categories['data'][$i] = $this->formatCategory($item);
            $i++;
        }

        return $categories;
    }

    public function getCategory($id)
    {
        return DB::table('categories')
            ->where('categories.id', '=', $id)
            ->first();
    }

    public function getCategoryById($id)
    {
        $category = $this->getCategory($id);

        return $this->formatCategory($category);
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

    public function saveCategoryWithImage($name, $description, $images)
    {
        $category = new Category([
            'name' => $name,
            'description' => $description,
        ]);
        $category->save();

        foreach ($images as $item) {
            $this->saveCategoryVIImage($category->id, $item['id']);
        }

        return $this->getCategoryById($category->id);
    }

    public function saveCategoryWithoutImage($name, $description)
    {
        $category = new Category([
            'name' => $name,
            'description' => $description,
        ]);
        $category->save();

        return $this->getCategoryById($category->id);
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
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, 'Validation failed', $validator->errors()->all());
        }

        $name = $request->get('name');
        $description = $request->get('description');
        $images = RequestFormatter::resolveJsonConfusion($request->get('images'));

        $category = null;
        if (is_null($images) || empty($images)) {
            $category = $this->updateCategoryWithoutImage($id, $name, $description);
        } else {
            if (sizeof($images) > 1) {
                return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, ["You can upload maximum 1 image"]);
            }

            $category = $this->updateCategoryWithImage($id, $images, $name, $description);
        }

        if (is_null($category)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Category successfully updated', $category, 'category', true);
    }

    public function updateCategoryWithImage($id, $images, $name, $description)
    {
        foreach ($images as $item) {
            $this->updateCategoryVImage($id, $item['id']);
        }

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

    public function deleteCategoryVIImage($category_id)
    {
        return DB::table('categories_v_images')
            ->where('categories_v_images.category_id', $category_id)
            ->delete();
    }

    public function getCategoryImage($category_id)
    {
        $imageMap = DB::table('categories_v_images')
            ->where('categories_v_images.category_id', $category_id)
            ->get();

        return $this->imageRepo->getRelationalImages($imageMap);
    }

    public function updateCategoryVImage($category_id, $image_id)
    {
        return DB::table('categories_v_images')
            ->where('categories_v_images.category_id', $category_id)
            ->update(
                [
                    'category_id' => $category_id,
                    'image_id' => $image_id
                ]
            );
    }

    public function deleteCategory($category_id)
    {
        $this->deleteCategoryVIImage($category_id);

        return DB::table('categories')
            ->where('categories.id', $category_id)
            ->delete();
    }

    public function destroyCategory($category_id)
    {
        $status = $this->deleteCategory($category_id);

        if ($status == false) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, "Unknown category", null);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Category successfully deleted', null, null, false);
    }
}
