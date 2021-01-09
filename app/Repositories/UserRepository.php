<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Role;
use App\RoleVUser;
use Log;



class UserRepository implements IUserRepository
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role'=> 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }


        $role = Role::select('id')->where("rolename", strtolower($request->role))->first();
        if (!$role) {
            return response()->json([
                'success' => false,
                'error' => "Role error!",
                'message' => 'Invalid role!'
            ], 400);
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        $roleVUser = new RoleVUser();
        $roleVUser->role_id = $role->id;
        $roleVUser->user_id = $user->id;
        $roleVUser->save();

        return response()->json([
            'success' => true,
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function logout() {
        auth()->logout();

        return response()->json([
            'success' => true,
            'message' => 'User successfully signed out']);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile() {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token){
        $user = auth()-> user();

        $userData = User::join('roles_v_users','users.id', '=', 'roles_v_users.user_id')
        ->join('roles','roles.id', '=', 'roles_v_users.role_id')
        ->select('users.id', 'users.username', 'users.email', 'roles.id as roleId', 'roles.rolename as role')
        ->where('users.id', $user->id)
        ->first();


        return response()->json([
            'success' => true,
            'message' => 'user successfully logged in',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => [
                'user_id' => $userData->id,
                'username' => $userData->username,
                'email' => $userData->email
            ],
            'role' => [
                'role_id' => $userData->roleId,
                'role_name' => $userData->role
            ]
        ],200);
    }
}
