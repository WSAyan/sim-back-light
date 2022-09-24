<?php


namespace App\Http\Controllers;


use App\Repositories\Home\IHomeRepository;
use App\Repositories\Auth\IUserRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private $homeRepo;
    private $userRepo;

    public function __construct(IHomeRepository $homeRepo, IUserRepository $userRepo)
    {
        $this->homeRepo = $homeRepo;
        $this->userRepo = $userRepo;
    }

    public function index()
    {
    }

    public function appData()
    {
        return $this->homeRepo->getAppData();
    }

    public function showUsersList(Request $request)
    {
        return $this->userRepo->users($request);
    }

    public function showRolesList(Request $request)
    {
        return $this->userRepo->roles($request);
    }

    public function getDropdowns()
    {
        return $this->homeRepo->getDropdowns();
    }
}
