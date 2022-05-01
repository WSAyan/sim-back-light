<?php

namespace App\Http\Controllers;

use App\Repositories\OTP\IOTPRepository;
use Illuminate\Http\Request;

class OTPController extends Controller
{
    private $otpRepo;

    public function __construct(IOTPRepository $otpRepo)
    {
        $this->otpRepo = $otpRepo;
    }

    public function showItems(Request $request)
    {
        return $this->otpRepo->getOTPList($request);
    }

    public function store(Request $request)
    {
        return $this->otpRepo->createOTP($request);
    }


    public function show($id)
    {
        return $this->otpRepo->showOTP($id);
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
