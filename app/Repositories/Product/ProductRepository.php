<?php


namespace App\Repositories\Product;


use App\Product;
use App\ProductVImage;
use App\ProductVOption;
use App\Repositories\Brand\IBrandRepository;
use App\Repositories\Category\ICategoryRepository;
use App\Repositories\Image\IImageRepository;
use App\Stock;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductRepository implements IProductRepository
{
    private $brandRepo, $categoryRepo, $imageRepo;

    public function __construct(IBrandRepository $brandRepo, ICategoryRepository $categoryRepo, IImageRepository $imageRepo)
    {
        $this->brandRepo = $brandRepo;
        $this->categoryRepo = $categoryRepo;
        $this->imageRepo = $imageRepo;
    }

    public function showProductList(Request $request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $products = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->selectRaw(
                "
                            products.id as product_id,
                            products.name as product_name,
                            products.description as product_description,
                            products.price as product_unit_price,
                            products.stock_quantity as product_stock_quantity,
                            categories.id as category_id,
                            categories.name as category_name,
                            brands.id as brand_id,
                            brands.brand_name as brand_name,
                            units.id as unit_id,
                            units.unit_name as unit_name,
                            units.is_reminder_allowed as unit_reminder_allowed
                "
            )
            ->where('products.name', 'LIKE', "%{$query}%")
            ->groupBy('products.id')
            ->orderBy('products.id', 'desc')
            ->paginate($size)
            ->toArray();

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Products list generated', $this->formatProducts($products), 'products', true);
    }

    public function getProductImage($product_id)
    {
        $imageMap = DB::table('products_v_images')
            ->where('products_v_images.product_id', $product_id)
            ->get();

        return $this->imageRepo->getRelationalImages($imageMap);
    }

    /**
     * format product item
     * @param $product
     * @return array
     */
    private function formatProduct($product)
    {
        $productDetails = [];
        $productDetails['id'] = $product->product_id;
        $productDetails['name'] = $product->product_name;
        $productDetails['description'] = $product->product_description;
        $productDetails['price'] = $product->product_unit_price;
        $productDetails['stock_quantity'] = $product->product_stock_quantity;
        $productDetails['category']['id'] = $product->category_id;
        $productDetails['category']['name'] = $product->category_name;
        $productDetails['category']['images'] = $this->categoryRepo->getCategoryImage($product->category_id);
        $productDetails['brand']['id'] = $product->brand_id;
        $productDetails['brand']['name'] = $product->brand_name;
        $productDetails['brand']['images'] = $this->categoryRepo->getCategoryImage($product->brand_id);
        $productDetails['unit']['id'] = $product->unit_id;
        $productDetails['unit']['name'] = $product->unit_name;
        $productDetails['unit']['is_reminder_allowed'] = $product->unit_reminder_allowed;
        $productDetails['images'] = $this->getProductImage($product->product_id);
        $result['product_options'] = $this->getProductOptionsWithDetails($product->product_id);

        return $productDetails;
    }

    /**
     * formats product list for response
     * @param $products
     * @return mixed
     */
    private function formatProducts($products)
    {
        $data = $products['data'];
        $i = 0;
        foreach ($data as $item) {
            $products['data'][$i] = $this->formatProduct($item);
            $i++;
        }

        return $products;
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'brand_id' => 'required',
            'unit_id' => 'required',
            'price' => 'required',
            'has_options' => 'required',
            'stock_quantity' => 'required',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, $validator->errors()->all());
        }

        $category_id = $request->get('category_id');
        $brand_id = $request->get('brand_id');
        $unit_id = $request->get('unit_id');
        $price = $request->get('price');
        $name = $request->get('name');
        $description = $request->get('description');
        $has_options = $request->get('has_options');
        $product_details = json_decode($request->get('product_details'), true);
        $stock_quantity = $request->get('stock_quantity');
        $images = json_decode($request->get('images'), true);

        if (sizeof($images) > 5) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, ["You can upload maximum 5 images"]);
        }

        $product = $this->saveProduct($category_id, $brand_id, $unit_id, $price, $name, $description, $has_options, $stock_quantity);

        foreach ($images as $item) {
            $this->saveProductVImage($product->id, $item);
        }

        $brand_name = $this->brandRepo->getBrand($brand_id)->brand_name;
        $category_name = $this->categoryRepo->getCategory($category_id)->name;

        if ($has_options == false) {
            // saving only one sku
            $stock = $this->saveStock($product->id, $this->generateSku($name, $brand_name, $category_name), $stock_quantity);
            if (is_null($stock)) {
                return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
            }
        } else {
            // saving multiple sku depending on options
            foreach ($product_details as $item) {
                $product_options_id = $item['product_options_id'];
                $product_options_details_id = $item['product_options_details_id'];
                $quantity = $item['quantity'];

                $stock = $this->saveStock($product->id, $this->generateSku($name, $brand_name, $category_name), $quantity);
                if (is_null($stock)) {
                    return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
                }

                $productVOption = $this->saveProductVOption($product->id, $product_options_id, $product_options_details_id, $stock->id);
                if (is_null($productVOption)) {
                    return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
                }
            }
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_CREATE, 'Product successfully created', $this->getProductDetailsById($product->id), 'product', true);
    }

    public function showProduct($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Product showed',
            'product' => $this->getProductDetailsById($id)
        ]);
    }

    public function getProductById($id)
    {
        return DB::table('products')->where('id', $id)->first();
    }

    /**
     * @param $id : product id
     * @return array
     */
    public function getProductDetailsById($id)
    {
        // get product
        $product = DB::table('products')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('brands', 'brands.id', '=', 'products.brand_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->selectRaw(
                "
                            products.id as product_id,
                            products.name as product_name,
                            products.description as product_description,
                            products.price as product_unit_price,
                            products.stock_quantity as product_stock_quantity,
                            categories.id as category_id,
                            categories.name as category_name,
                            brands.id as brand_id,
                            brands.brand_name as brand_name,
                            units.id as unit_id,
                            units.unit_name as unit_name,
                            units.is_reminder_allowed as unit_reminder_allowed
                "
            )
            ->where('products.id', $id)
            ->first();

        // process query results
        if (is_null($product)) return null;

        return $this->formatProduct($product);
    }

    /**
     * @param $id : product id
     * @return array
     */
    public function getProductOptionsWithDetails($id)
    {
        // get product options
        $productsOptions = $this->getProductOptionsWithProduct($id);

        $productOptionsAndDetails = [];
        $i = 0;
        foreach ($productsOptions as $item) {
            $productOptionsAndDetails[$i]['product_options_id'] = $item->product_options_id;
            $productOptionsAndDetails[$i]['product_options_name'] = $item->product_options_name;

            // get product details
            $details = DB::table('products_v_options')
                ->join('product_options_details', 'products_v_options.product_options_details_id', '=', 'product_options_details.id')
                ->join('stocks', 'products_v_options.stock_id', '=', 'stocks.id')
                ->select(
                    'product_options_details.id as id',
                    'product_options_details.name as name',
                    'stocks.id as stock_id',
                    'stocks.quantity as quantity',
                    'stocks.sku as sku'
                )
                ->where('products_v_options.product_id', $id)
                ->where('products_v_options.product_options_id', $item->product_options_id)
                ->get();

            $productOptionsAndDetails[$i]['product_details'] = $details;
            $i++;
        }

        return $productOptionsAndDetails;
    }

    /**
     * @param $id : product id
     * @return \Illuminate\Support\Collection
     */
    public function getProductOptionsWithProduct($id)
    {
        return DB::table('products_v_options')
            ->join('product_options', 'products_v_options.product_options_id', '=', 'product_options.id')
            ->select(
                'product_options.id as product_options_id',
                'product_options.name as product_options_name'
            )
            ->where('products_v_options.product_id', $id)
            ->groupBy('product_options_id')
            ->get();
    }

    /**
     * generates skus for stock
     * @param $productName
     * @param $brandName
     * @param $categoryName
     * @return string|void
     */
    public function generateSku($productName, $brandName, $categoryName)
    {
        if ($productName == null) return;

        if ($brandName == null) return;

        if ($categoryName == null) return;

        if (strlen($productName) < 2) return;

        if (strlen($brandName) < 2) return;

        if (strlen($categoryName) < 2) return;

        $productName = strtoupper($productName);
        $brandName = strtoupper($brandName);
        $categoryName = strtoupper($categoryName);

        $stockId = DB::table('stocks')->max('id');
        if ($stockId == null) $stockId = 0;
        $stockId++;

        $stockId = strtoupper(dechex($stockId));

        return $stockId . substr($productName, 0, 2) . substr($brandName, 0, 2) . substr($categoryName, 0, 2);
    }

    public function updateProductStock($product_id, $stock_id, $quantity)
    {
        $product = DB::table('products')->where('id', $product_id)->first();
        $updatedQuantity = $product->stock_quantity - $quantity;
        $updatedProduct = DB::table('products')
            ->where('id', $product_id)
            ->update(['stock_quantity' => $updatedQuantity]);

        $stock = DB::table('stocks')->where('id', $stock_id)->first();
        $updatedQuantity = $stock->quantity - $quantity;
        $updatedStock = DB::table('stocks')
            ->where('id', $stock_id)
            ->update(['quantity' => $updatedQuantity]);
    }

    public function saveProduct($category_id, $brand_id, $unit_id, $price, $name, $description, $has_options, $stock_quantity)
    {
        $product = new Product([
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'unit_id' => $unit_id,
            'price' => $price,
            'name' => $name,
            'description' => $description,
            'has_options' => $has_options,
            'stock_quantity' => $stock_quantity
        ]);
        $product->save();

        return $product;
    }

    public function saveStock($product_id, $sku, $stock_quantity)
    {
        $stock = new Stock([
            'product_id' => $product_id,
            'sku' => $sku,
            'quantity' => $stock_quantity
        ]);
        $stock->save();

        return $stock;
    }

    public function saveProductVOption($product_id, $product_options_id, $product_options_details_id, $stock_id)
    {
        $productVOption = new ProductVOption([
            'product_id' => $product_id,
            'product_options_id' => $product_options_id,
            'product_options_details_id' => $product_options_details_id,
            'stock_id' => $stock_id
        ]);
        $productVOption->save();

        return $productVOption;
    }

    public function saveProductVImage($product_id, $image_id)
    {
        $productVImage = new ProductVImage([
            'product_id' => $product_id,
            'image_id' => $image_id,
        ]);
        $productVImage->save();

        return $productVImage;
    }

    public function getProductImages($product_id)
    {
        $imageUrl = asset('images') . '/';

        $images = DB::table('products_v_images')
            ->join('images', 'products_v_images.image_id', '=', 'images.id')
            ->selectRaw("images.id as image_id, images.image as image_name, CONCAT('$imageUrl' , images.image) as image_url")
            ->where('product_id', $product_id)
            ->get();

        return $images;
    }
}
