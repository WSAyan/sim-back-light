<?php


namespace App\Repositories\Home;

use App\Repositories\BaseRepository;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;

class HomeRepository extends BaseRepository implements IHomeRepository
{

    public function getAppData()
    {
        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'App data',
            $this->formatData(),
            'app_data',
            true
        );
        return response()->json([
            'success' => true,
            'message' => 'App data',
            'drawer_menu_items' => $this->getDrawerMenuItems()
        ]);
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
}
