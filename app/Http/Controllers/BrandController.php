<?php


namespace App\Http\Controllers;


use App\Repositories\Brand\IBrandRepository;
use App\Repositories\Order\IOrderRepository;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    private $brandRepo;

    public function __construct(IBrandRepository $brandRepo)
    {
        $this->brandRepo = $brandRepo;
    }

    public function showBrandsList(Request $request)
    {
        return $this->brandRepo->showAllBrands($request);
    }

    public function store(Request $request)
    {
        return $this->brandRepo->storeBrand($request);
    }

    public function show($id)
    {
        return $this->brandRepo->showBrandDetails($id);
    }

    public function update(Request $request, $id)
    {
        return $this->brandRepo->updateBrand($request, $id);
    }
}
