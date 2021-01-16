<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Repositories\IUserRepository;
use Validator;
use App\User;
use App\Role;
use App\RoleVUser;
use Log;


class AuthController extends Controller
{

    private $userRepo;

    public function __construct(IUserRepository $userRepo)
    {

        $this->middleware('auth:api', ['except' => ['login', 'register']]);

        $this->userRepo = $userRepo;

    }

    public function login(Request $request)
    {
        return $this->userRepo->login($request);
    }

    public function register(Request $request)
    {
        return $this->userRepo->register($request);
    }

    public function logout()
    {
        return $this->userRepo->logout();
    }

    public function refresh()
    {
        return $this->userRepo->refresh();
    }

    public function userProfile()
    {
        return $this->userRepo->userProfile();
    }

}
