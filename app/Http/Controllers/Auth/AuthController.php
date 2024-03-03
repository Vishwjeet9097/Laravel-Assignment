<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\AdminLoginRequest;
use App\Http\Requests\User\Auth\ForgotPasswordRequest;
use App\Http\Requests\User\Auth\ForgotPasswordUpdateRequest;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\RegisterRequest;
use App\Http\Requests\User\Auth\ResendOtpRequest;
use App\Http\Requests\User\Auth\UpdatePasswordRequest;
use App\Http\Requests\User\Auth\UpdateSelfRequest;
use App\Http\Requests\User\Auth\UpdateUserPasswordRequest;
use App\Http\Requests\User\Auth\VerifyContactRequest;
use App\Http\Requests\User\Auth\VerifyOtpRequest;
use App\Repositories\AuthRepository;
use Illuminate\Http\Request;
use Mail;
use App\Mail\SendOtpMail;

class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(RegisterRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->add($inputs);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->verifyOtp($inputs);
    }
    public function verifyForgotPasswordOtp(VerifyOtpRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->verifyForgotPasswordOtp($inputs);
    }
    public function ResendOtp(ResendOtpRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->resendOtp($inputs);
    }


    public function login(LoginRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->loginUser($inputs);
    }

    public function adminLogin(AdminLoginRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->loginUser($inputs);
    }

    public function detail(Request $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->detail($inputs);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->forgotPassword($inputs);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->authRepository->forgotPasswordUpdate($inputs);
    }

    public function logout(Request $request)
    {
        return $login = $this->authRepository->logoutUser($request);
    }

    public function self(Request $request)
    {
        return $authData = $this->authRepository->self($request);
    }

    public function updateSelfData(UpdateSelfRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        $reqData = $this->trimKeys($inputs, ['name', 'email', 'password', 'contact']);
        return $this->authRepository->selfUpdate($reqData, $inputs);
    }

    public function updateSelfPassword(UpdateUserPasswordRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters()
        );
        $reqData = $this->trimKeys($inputs, ['currentPassword', 'newPassword']);
        return $this->authRepository->updatePassword($reqData, $inputs);
    }

    public function trimKeys($dataArr, $exceptKeysArr)
    {
        if ($dataArr && count($dataArr) > 0 && $exceptKeysArr && count($exceptKeysArr) > 0) {
            $finalArr = [];
            foreach ($exceptKeysArr as $except) {
                if (array_key_exists($except, $dataArr)) {
                    $finalArr[$except] = $dataArr[$except];
                }
            }
            return $finalArr;
        } else {
            return [];
        }
    }

    public function trimBlankKeys($keysArr)
    {
        if ($keysArr && count($keysArr) > 0) {
            $finalArr = [];
            foreach ($keysArr as $keys => $value) {
                if ($value && $value !== '') {
                    $finalArr[$keys] = $value;
                }
            }
            return $finalArr;
        } else {
            return [];
        }
    }
}