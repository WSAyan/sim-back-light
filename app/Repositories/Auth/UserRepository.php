<?php

namespace App\Repositories\Auth;

use App\Role;
use App\RoleVUser;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\AccountDetail;
use App\Utils\RequestFormatter;
use App\Utils\ResponseFormatter;
use App\Repositories\Image\IImageRepository;
use App\UserDetail;

define('ADMIN_ROLE_LEVEL', 2);

class UserRepository implements IUserRepository
{
    private $imageRepo;

    public function __construct(IImageRepository $imageRepo)
    {
        $this->imageRepo = $imageRepo;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100',
            'email' => 'required|string|min:4|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role_id' => 'required|numeric',
            'full_name' => 'required|string|between:2,300',
            'phone' => 'string|between:2,300',
            'address' => 'required|string|between:2,300',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_VALIDATION,
                VALIDATION_ERROR_MESSAGE,
                $validator->errors()->all(),
            );
        }


        $role = Role::select('id')->where("id", $request->role_id)->first();
        if (!$role) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_VALIDATION,
                VALIDATION_ERROR_MESSAGE,
                ["Invalid role"],
            );
        }

        // create user
        $user = $this->createUser($validator, $request->password);

        // set user role
        $roleVUser = $this->setUserRole($role->id, $user->id);

        // set user details, setting email as phone for now
        $userDetails = $this->setUserDetails($user->id, null, $request->full_name, $request->email, $request->address);

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_CREATE,
            'User successfully registered',
            $this->formatUser($user),
            'user',
            true
        );
    }

    /**
     * create user
     */
    private function createUser($validator, $password)
    {
        return User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($password)]
        ));
    }

    /**
     * set user role
     */
    private function setUserRole($role_id, $user_id)
    {
        $roleVUser = new RoleVUser();
        $roleVUser->role_id = $role_id;
        $roleVUser->user_id = $user_id;
        $roleVUser->save();

        return $roleVUser;
    }


    /**
     * set user details
     */
    private function setUserDetails($user_id, $location_id, $full_name, $phone, $address)
    {
        $userDetails = new UserDetail();
        $userDetails->user_id = $user_id;
        $userDetails->location_id = $location_id;
        $userDetails->full_name = $full_name;
        $userDetails->phone = $phone;
        $userDetails->address = $address;
        $userDetails->track_id = uniqid('rt#');
        $userDetails->save();

        return $userDetails;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_VALIDATION,
                VALIDATION_ERROR_MESSAGE,
                $validator->errors()->all(),
            );
        }


        if (!$token = auth()->attempt($validator->validated())) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_UNAUTHORIZED,
                "Authorization failed",
                null
            );
        }

        return $this->createNewToken($token);
    }

    public function logout()
    {
        auth()->logout();

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'User successfully signed out',
            null,
            null,
            false
        );
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile()
    {
        $user = auth()->user();

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'User profile',
            $this->formatUser($user),
            "user",
            true
        );
    }

    public function hasAdminPrivilage($user_id)
    {
        return $this->getUserRole($user_id)?->id <= ADMIN_ROLE_LEVEL;
    }

    public function users(Request $request)
    {
        if (!$this->hasAdminPrivilage(auth()->user()->id)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_UNAUTHORIZED, "Permission denied!", ["This user has no permission"]);
        }


        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $users = DB::table('users')
            ->selectRaw(
                "users.id as id,
                users.username as username,
                users.email as email"
            )
            ->where('users.username', 'LIKE', "%{$query}%")
            ->orderBy('users.id')
            ->paginate($size)
            ->toArray();

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Users list generated', $this->formatUsers($users), 'users', true);
    }


    public function roles(Request $request)
    {
        if (!$this->hasAdminPrivilage(auth()->user()->id)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_UNAUTHORIZED, "Permission denied!", ["This user has no permission"]);
        }

        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 20;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }

        $items = DB::table('roles')
            ->selectRaw(
                "roles.id as id,
                roles.rolename as rolename"
            )
            ->where('roles.rolename', 'LIKE', "%{$query}%")
            ->orderBy('roles.id')
            ->paginate($size)
            ->toArray();

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Roles list generated', $this->formatRoles($items), 'roles', true);
    }

    public function getuser($id)
    {
        $user = DB::table('users')
            ->selectRaw(
                "users.id as id,
                users.username as username,
                users.email as email"
            )
            ->where('users.id', '=', $id)
            ->first();

        return $this->formatUser($user);
    }

    private function formatRoles($items)
    {
        $data = $items['data'];
        $i = 0;
        foreach ($data as $item) {
            $items['data'][$i] = $this->formatRole($item);
            $i++;
        }

        return $items;
    }

    private function formatRole($role)
    {
        $item = [];
        $item['id'] = $role->id;
        $item['rolename'] = $role->rolename;

        return $item;
    }

    private function formatUsers($users)
    {
        $data = $users['data'];
        $i = 0;
        foreach ($data as $item) {
            $users['data'][$i] = $this->formatUser($item);
            $i++;
        }

        return $users;
    }

    private function formatUser($user)
    {
        $result = [];
        $result['id'] = $user->id;
        $result['username'] = $user->username;
        $result['email'] = $user->email;


        $user_details = $this->getUserDetails($user->id);
        $result['full_name'] = $user_details?->full_name;
        $result['phone'] = $user_details?->phone;
        $result['address'] = $user_details?->address;
        $result['track_id'] = $user_details?->track_id;
        $result['active_status'] = $user_details?->active_status;


        $result['role'] = $this->getUserRole($user->id);
        $result['images'] = $this->getUserImage($user->id);

        return $result;
    }

    public function getUserImage($user_id)
    {
        $imageMap = DB::table('users_v_images')
            ->where('users_v_images.user_id', $user_id)
            ->get();

        return $this->imageRepo->getRelationalImages($imageMap);
    }

    public function getUserDetails($user_id)
    {
        return DB::table('user_details')
            ->select(
                'user_details.phone as phone',
                'user_details.full_name as full_name',
                'user_details.address as address',
                'user_details.track_id as track_id',
                'user_details.active_status as active_status',
            )
            ->where('user_details.user_id', $user_id)
            ->first();
    }

    public function getUserRole($userID)
    {
        return User::join('roles_v_users', 'users.id', '=', 'roles_v_users.user_id')
            ->join('roles', 'roles.id', '=', 'roles_v_users.role_id')
            ->select('roles.id as id', 'roles.rolename as rolename')
            ->where('users.id', $userID)
            ->first();
    }

    protected function createNewToken($token)
    {
        $user = auth()->user();

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'User successfully logged in',
            $this->formatLoginData($user, $token),
            'data',
            true
        );
    }

    private function formatLoginData($user, $token)
    {
        $data = [];
        $data["access_token"] = $token;
        $data["token_type"] = "Bearer";
        $data['user'] = $this->formatUser($user);

        return $data;
    }
}
