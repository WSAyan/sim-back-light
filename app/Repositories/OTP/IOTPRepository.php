<?php


namespace App\Repositories\OTP;


use Illuminate\Http\Request;

interface IOTPRepository
{
    public function getOTPList(Request $request);

    public function createOTP(Request $request);

    public function showOTP($id);

    public function updateOTP($request, $id);

    public function verifyOTP($request, $id);

    public function destroyOTP($id);
}
