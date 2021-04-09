<?php


namespace App\Http\Controllers;


use App\Repositories\Home\IHomeRepository;

class HomeController extends Controller
{
    private $homeRepo;

    public function __construct(IHomeRepository $homeRepo)
    {
        $this->homeRepo = $homeRepo;
    }

    public function index()
    {

    }

    public function appData()
    {
        return $this->homeRepo->getAppData();
    }
}
