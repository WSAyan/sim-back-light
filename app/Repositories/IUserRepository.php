<?php
namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Role;
use App\RoleVUser;
use Log;

interface IUserRepository
{
    public function register(Request $request);

    public function login(Request $request);

    public function logout();

    public function refresh();

    public function userProfile();
}
