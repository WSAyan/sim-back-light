<?php


namespace App\Repositories\Home;

use App\Repositories\BaseRepository;
use App\Repositories\Brand\IBrandRepository;
use App\Repositories\Category\ICategoryRepository;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;

class HomeRepository extends BaseRepository implements IHomeRepository
{
    private $categroyRepo, $brandRepo;

    public function __construct(ICategoryRepository $categroyRepo, IBrandRepository $brandRepo)
    {
        $this->categroyRepo = $categroyRepo;
        $this->brandRepo = $brandRepo;
    }

    public function getAppData()
    {
        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'App data',
            $this->formatData(),
            'app_data',
            true
        );
    }

    public function getDrawerMenuItems()
    {
        return DB::table('screens')
            ->get();
    }

    private function formatData()
    {
        return [
            "drawer_menu_items" => $this->getDrawerMenuItems(),
            "main_account" => MAIN_ACCOUNT
        ];
    }

    public function getDropdowns()
    {
        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'dropdown data',
            [
                "categories" => $this->categroyRepo->getCategories(),
                "brands" => $this->brandRepo->getAllBrands(),
                "units" => DB::table('units')->get(),
            ],
            'data',
            true
        );
    }
}
