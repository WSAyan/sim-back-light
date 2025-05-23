<?php

namespace App\Repositories\OTP;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\OTP;
use App\Repositories\Auth\IUserRepository;
use Illuminate\Support\Facades\Storage;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

define("OTP_TIMEOUT", 60000);

class OTPRepository implements IOTPRepository
{
    private $userRepo;

    public function __construct(IUserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getOTPList(Request $request)
    {
        $size = $request->get('size');
        if (is_null($size) || empty($size)) {
            $size = 5;
        }

        $query = $request->get('query');
        if (is_null($query) || empty($query)) {
            $query = "";
        }


        $OTPs = $this->getOTPs($size, $query);

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'OTPs list generated', $this->formatOTPs($OTPs), 'OTPs', false);
    }

    private function getOTPById($id)
    {
        return DB::table('otp')
            ->select("*")
            ->where('otp.id', '=', $id)
            ->first();
    }

    private function getOTPs($size, $query)
    {
        return DB::table('otp')
            ->select("*")
            ->where('otp.phone_number', 'LIKE', "%{$query}%")
            ->orderBy('otp.id')
            ->paginate($size)
            ->toArray();
    }

    private function formatOTPs($OTPs)
    {
        $data = $OTPs['data'];
        $i = 0;
        foreach ($data as $item) {
            $OTPs['data'][$i] = $this->formatOTP($item);
            $i++;
        }

        return $OTPs;
    }


    public function createOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to_user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, $validator->errors()->all());
        }

        $otpValidator = $this->validateOTP(
            $request->get('to_user_id')
        );

        if ($otpValidator['isInvalid']) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_VALIDATION,
                VALIDATION_ERROR_MESSAGE,
                $otpValidator['errors'],
            );
        }

        $toUser = $this->userRepo->getuserById($request->get('to_user_id'));
        if (is_null($toUser['phone']) || empty($toUser['phone'])) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, ["Phone number not found"]);
        }

        $otpCallback = $this->sendOTP($toUser['phone']);
        if (!$otpCallback['response']->ok() || $otpCallback['response']->status() != 200) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE, ["otp sending failed!"]);
        }

        $OTP = $this->saveOTP(
            $toUser['id'],
            $toUser['phone'],
            $otpCallback['otp'],
            $otpCallback['message_body'],
            OTP_TIMEOUT
        );

        if (is_null($OTP) || empty($OTP)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_COMMON, COMMON_ERROR_MESSAGE);
        }

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_CREATE,
            'OTP created',
            $this->formatOTP($OTP),
            'OTP',
            true
        );
    }

    private function formatOTP($OTP)
    {
        $result = [];
        $result['id'] = $OTP->id;
        $result['to_user'] = $this->userRepo->getuserById($OTP->to_user_id);
        $result['phone_number'] = $OTP->phone_number;
        $result['timeout'] = $OTP->timeout;

        return $result;
    }

    private function saveOTP($to_user_id, $phone_number, $otp, $message_body, $timeout)
    {
        $OTP = new OTP(
            [
                'to_user_id' => $to_user_id,
                'phone_number' => $phone_number,
                'otp' => $otp,
                'message_body' => $message_body,
                'timeout' => $timeout
            ]
        );

        $OTP->save();

        return $OTP;
    }

    private function sendOTP($phone_number)
    {
        $otp = $this->generateRandOTP();
        $message_body = $this->generateRandOTPMessage($otp);

        $response = Http::asForm()->post(env('SMS_BASE_URL'), [
            'username' => env('SMS_USER'),
            'password' => env('SMS_PASSWORD'),
            'number' => $phone_number,
            'message' => $message_body,
        ]);

        return [
            'response' => $response,
            'otp' => $otp,
            'message_body' => $message_body,
        ];
    }

    private function generateRandOTP()
    {
        return rand(1001, 9999);
    }

    private function generateRandOTPMessage($otp)
    {
        return "Your code is " . $otp;
    }

    private function validateOTP($to_user_id)
    {
        $errors = [];
        $isInvalid = false;

        $toUser = $this->userRepo->getuser($to_user_id);
        if (is_null($toUser) || empty($toUser)) {
            array_push($errors, "User not found!");
            $isInvalid = true;
        }

        return [
            'isInvalid' => $isInvalid,
            'errors' => $errors,
        ];
    }

    public function showOTP($id)
    {
        $OTP = $this->getOTPById($id);
        if (is_null($OTP) || empty($OTP)) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_NOT_FOUND, "OTP doesn't exist", ["invalid OTP id"]);
        }

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'OTP data',
            $this->formatOTP($OTP),
            'OTP',
            false
        );
    }


    public function verifyOTP($request, $id)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_VALIDATION, VALIDATION_ERROR_MESSAGE, $validator->errors()->all());
        }

        $otp = $request->get('otp');
        $last_otp_data = $this->getOTPById($id);

        if (!$this->isOtpVerificationSucced($otp, $last_otp_data)) {
            return ResponseFormatter::errorResponse(
                ERROR_TYPE_NOT_FOUND,
                "OTP verification failed",
                ["OTP not found or verification failed"]
            );
        }

        $this->updateVerfiedOTP($last_otp_data->id);

        return ResponseFormatter::successResponse(
            SUCCESS_TYPE_OK,
            'OTP verified'
        );
    }

    private function isOtpVerificationSucced($otp, $last_otp_data)
    {
        return $last_otp_data?->is_verified != 1 && $otp == $last_otp_data?->otp;
    }

    private function updateVerfiedOTP($id)
    {
        return DB::table('otp')
            ->where('otp.id', $id)
            ->update([
                'is_verified' => true,
            ]);
    }

    public function updateOTP($request, $id)
    {
    }


    public function destroyOTP($id)
    {
        $status = $this->deleteOTP($id);

        if ($status == false) {
            return ResponseFormatter::errorResponse(ERROR_TYPE_NOT_FOUND, "OTP doesn't exist", []);
        }

        return ResponseFormatter::successResponse(SUCCESS_TYPE_OK, 'OTP successfully deleted', null, "OTP", false);
    }

    private function deleteOTP($id)
    {
        return DB::table('otp')
            ->where('otp.id', $id)
            ->delete();
    }
}
