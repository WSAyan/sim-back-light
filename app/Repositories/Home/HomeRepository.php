<?php


namespace App\Repositories\Home;


use Illuminate\Support\Facades\DB;

class HomeRepository implements IHomeRepository
{

    public function getAppData()
    {
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
}
