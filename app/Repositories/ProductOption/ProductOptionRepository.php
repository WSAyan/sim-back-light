<?php


namespace App\Repositories\ProductOption;

use App\Product;
use App\ProductVImage;
use App\ProductVOption;
use App\Repositories\Brand\IBrandRepository;
use App\Repositories\Category\ICategoryRepository;
use App\Repositories\Image\IImageRepository;
use App\Stock;
use App\Utils\RequestFormatter;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductOptionRepository implements IProductOptionRepository
{
    public function showProductOptions($request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $productOptions = DB::table('product_options')
            ->selectRaw(
                "
                    product_options.id as id,
                    product_options.name as name
                "
            )
            ->where('product_options.name', 'LIKE', "%{$query}%")
            ->groupBy('product_options.id')
            ->orderBy('product_options.id', 'desc')
            ->paginate($size)
            ->toArray();


        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Products list generated', $this->formatProductOptions($productOptions), 'products', true);
    }

    public function storeProductOption($request)
    {
    }

    public function showProductOption($id)
    {
    }

    public function updatePrductOption(Request $request, $id)
    {
    }

    public function destroyProductOption($id)
    {
    }

    private function formatProductOptions($productOption)
    {
        $data = $productOption['data'];
        $i = 0;
        foreach ($data as $item) {
            $products['data'][$i] = $this->formatProduct($item);
            $i++;
        }

        return $products;
    }

    public function formatProduct($productOption)
    {
        $productOptionsAndDetails = [];
        $productOptionsAndDetails['id'] = $productOption->id;
        $productOptionsAndDetails['name'] = $productOption->name;

        $details = DB::table('product_options_details')
            ->where('product_options_id', $productOption->id)
            ->get();


        $productOptionsAndDetails['product_details'] = $details;


        return $productOptionsAndDetails;
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
            $productOptionsAndDetails[$i]['id'] = $item->product_options_id;
            $productOptionsAndDetails[$i]['name'] = $item->product_options_name;

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
     * gets products options
     */
    public function getProductOptions()
    {
        $productOptions = DB::table('product_options')->get();
        $productOptionsAndDetails = [];
        $i = 0;
        foreach ($productOptions as $item) {
            $details = DB::table('product_options_details')
                ->where('product_options_id', $item->id)
                ->get();

            $productOptionsAndDetails[$i]['id'] = $item->id;
            $productOptionsAndDetails[$i]['name'] = $item->name;
            $productOptionsAndDetails[$i]['product_details'] = $details;
            $i++;
        }

        return $productOptionsAndDetails;
    }
}
