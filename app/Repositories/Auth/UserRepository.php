<?php

namespace App\Repositories\Auth;

use App\Account;
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
use App\Transaction;
use App\UserDetail;
use Exception;
use App\Repositories\Account\IAccountRepository;
use App\Repositories\BaseRepository;
use App\Repositories\Contact\IContactRepository;

class UserRepository extends BaseRepository implements IUserRepository
{
    private $imageRepo, $accountRepo, $contactRepo;

    public function __construct(IImageRepository $imageRepo, IAccountRepository $accountRepo, IContactRepository $contactRepo)
    {
        $this->imageRepo = $imageRepo;
        $this->accountRepo = $accountRepo;
        $this->contactRepo = $contactRepo;
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
            'balance' => 'numeric'
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

        // creates account
        $account = $this->accountRepo->createAccount($user->id);

        // initial transaction
        $inital_transaction = $this->accountRepo->createTransaction(MAIN_ACCOUNT, $account->account_no, $request->balance ?: 0);

        // initial contact
        $contact = $this->contactRepo->createContactWithData(
            $user->id,
            $user->username,
            $userDetails->phone,
            null,
            $userDetails->address,
            null
        );

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

        $role_id = $request->get('role_id');
        if (is_null($role_id) || empty($role_id)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, ["role id required"]);
        }

        $users = $this->getUsersByRole($role_id, $size, $query);
        if (is_null($users) || empty($users)) {
            return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Users not found', null, 'users', true);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'Users list generated', $this->formatUsers($users), 'users', false);
    }


    private function getUsers($size, $query)
    {
        return DB::table('users')
            ->selectRaw(
                "users.id as id,
            users.username as username,
            users.email as email"
            )
            ->where('users.username', 'LIKE', "%{$query}%")
            ->orderBy('users.id')
            ->paginate($size)
            ->toArray();
    }

    private function getUsersByRole($role_id, $size, $query)
    {
        return DB::table('users')
            ->join('roles_v_users', 'users.id', '=', 'roles_v_users.user_id')
            ->selectRaw(
                "users.id as id,
            users.username as username,
            users.email as email"
            )
            ->where('roles_v_users.role_id', $role_id)
            ->where('users.username', 'LIKE', "%{$query}%")
            ->orderBy('users.id')
            ->paginate($size)
            ->toArray();
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

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'User details', $this->formatUser($user), 'user', true);
    }

    public function getuserById($id)
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
        $user_details = $this->getUserDetails($user->id);

        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user_details?->full_name,
            'phone' =>  $user_details?->phone,
            'address' => $user_details?->address,
            'track_id' => $user_details?->track_id,
            'active_status' => $user_details?->active_status,
            'role' => $this->getUserRole($user->id),
            'account' => $this->accountRepo->getUserAccountByUserID($user->id),
            'contacts' => $this->contactRepo->getContactsByUserId($user->id),
            'images' => $this->getUserImage($user->id),
        ];
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

    private function deleteUserVImage($id)
    {
        return DB::table('users_v_images')
            ->where('users_v_images.user_id', $id)
            ->delete();
    }

    private function deleteUserDetails($id)
    {
        $location_id = DB::table('user_details')
            ->select('user_details.location_id')
            ->where('user_details.user_id', $id)
            ->first();

        if (is_null($location_id) || empty($location_id)) {
            $this->deleteUsersLocation($location_id);
        }

        return DB::table('user_details')
            ->where('user_details.user_id', $id)
            ->delete();
    }

    private function deleteRoleVUser($id)
    {
        return DB::table('roles_v_users')
            ->where('roles_v_users.user_id', $id)
            ->delete();
    }

    private function deleteUsersLocation($id)
    {
        return DB::table('locations')
            ->where('locations.id', $id)
            ->delete();
    }

    private function deleteUser($id)
    {
        $this->deleteUserDetails($id);
        $this->deleteRoleVUser($id);
        $this->deleteUsersLocation($id);
        $this->deleteUserVImage($id);

        return DB::table('users')
            ->where('users.id', $id)
            ->delete();
    }

    public function destroyUser($id)
    {
        $status = $this->deleteUser($id);

        if ($status == false) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_NOT_FOUND, "User doesn't exist", []);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'User successfully deleted', null, "user", false);
    }

    public function updateUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'email' => 'required|string',
            'full_name' => 'required|string',
            'address' => 'required|string'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, 'Validation failed', $validator->errors()->all());
        }

        $user = $this->updateUsersValues(
            $id,
            $request->username,
            $request->email,
            $request->full_name,
            $request->address,
            $request->email
        ); // setting email as phone for now
        if (is_null($user) || empty($user)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, null);
        }


        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'User successfully updated', $user, 'user', true);
    }

    private function updateUsersValues($id, $usename, $email, $full_name, $address, $phone)
    {
        try {
            $this->updateuserDetails($id, $full_name, $address, $phone);

            DB::table('users')
                ->where('users.id', $id)
                ->update(
                    [
                        'username' => $usename,
                        'email' => $email,
                    ]
                );
        } catch (\Exception $e) {
            return null;
        }

        return $this->getuser($id);
    }

    private function updateuserDetails($user_id, $full_name, $address, $phone)
    {
        return DB::table('user_details')
            ->where('user_details.user_id', $user_id)
            ->update(
                [
                    'full_name' => $full_name,
                    'address' => $address,
                    'phone' => $phone,
                ]
            );
    }
}
