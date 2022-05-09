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

    public function users(Request $request);

    public function roles(Request $request);

    public function getuser($id);

    public function hasAdminPrivilage($user_id);

    public function destroyUser($id);

    public function updateUser(Request $request, $id);

    public function getuserById($id);

    public function getUserAccountByUserID($user_id);

    public function createTransaction($from_account_no, $to_account_no, $amount);
}
