<?php


namespace App\Repositories\Home;


interface IHomeRepository
{
    public function getAppData();

    public function getDrawerMenuItems();

    public function getDropdowns();
}
