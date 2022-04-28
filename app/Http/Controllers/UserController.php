<?php

namespace App\Http\Controllers;

use App\Repositories\Auth\IUserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function showUsers(Request $request)
    {
        return $this->userRepo->users($request);
    }

    public function store(Request $request)
    {
        return $this->userRepo->register($request);
    }


    public function show($id)
    {
        return $this->userRepo->getuser($id);
    }


    public function update(Request $request, $id)
    {
        return $this->userRepo->updateUser($request, $id);
    }


    public function destroy($id)
    {
        return $this->userRepo->destroyUser($id);
    }
}
