<?php

namespace App\Repositories\Auth;

use Illuminate\Http\Request;

interface IUserRepository
{
    public function register(Request $request);

    public function login(Request $request);

    public function logout();

    public function refresh();

    public function userProfile();
}
