<?php

namespace App\Repositories;

use App\Models\Token;
use App\Models\User;
use App\Services\UtilityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\SendOtpMail;
use Mail;

class AuthRepository
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function add($request)
    {
        DB::beginTransaction();
        try {

            $request['password'] = md5($request['password']);
            $email = $request['email'];
            $request['user_type'] = array_key_exists('user_type', $request) ? $request['user_type'] : 'USER';
            $request['verification_otp'] = UtilityService::generateOtp();
            $user = $this->user::where('email', $email)
                ->first();

            if ($user) {
                if ($user->verification_status != 'VERIFIED') {
                    $userUpdate = $this->user::where('email', $email)
                        ->update([
                            'password' => $request['password'],
                            'email' => $request['email'],
                            'user_type' => $request['user_type'],
                            'status' => 'ACTIVE',
                            'verification_otp' => $request['verification_otp']
                        ]);
                    if ($userUpdate) {
                        $sendOtp = $this->sendOtp($request);
                        if ($sendOtp) {
                            $response = response()->json([
                                "status" => "success",
                                "message" => "Otp Sent Successfully",
                                "data" => [
                                    "message" => "Otp Sent Successfully"
                                ]
                            ], 200);
                        } else {
                            $response = response()->json([
                                "error" => "User registration failed",
                                "errorDetails" => "User registration failed, user already registered with us",
                                "type" => "RESOURCE_CREATION_FAILED"
                            ], 403);
                        }
                    }
                } else {
                    $response = response()->json([
                        "error" => "User registration failed",
                        "errorDetails" => "User registration failed, user already registered with us",
                        "type" => "RESOURCE_CREATION_FAILED"
                    ], 403);
                }
            } else {
                $userDetail = $this->user::create($request);
                if ($userDetail && $userDetail->id) {
                    $sendOtp = $this->sendOtp($request);
                    $response = response()->json([
                        "status" => "success",
                        "message" => "Otp sent to your email.",
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "User registration failed",
                        "errorDetails" => "User registration failed, Please try again",
                        "type" => "RESOURCE_CREATION_FAILED"
                    ], 500);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function selfUpdate($request, $input)
    {
        DB::beginTransaction();
        try {
            if (array_key_exists('password', $request)) {
                $request['password'] = md5($request['password']);
            }
            $user = $this->user::where('id', $input['user_id'])
                ->update($request);
            if ($user) {
                $response = response()->json([
                    "status" => "success",
                    "message" => "User profile updated successfully"
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "User profile updation failed",
                    "errorDetails" => "User profiles updation failed, Wrong old password provided",
                    "type" => "RESOURCE_UPDATING_FAILED"
                ], 500);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function loginUser($request)
    {
        DB::beginTransaction();
        try {
            $email = $request['email'];
            $password = md5($request['password']);
            $user = $this->user::with('files')->where('email', $email)->where('password', $password)->where([
                "status" => "ACTIVE",
                "verification_status" => "VERIFIED"
            ])->first();
            if ($user && $user->user_type) {
                if ($email && $password) {
                    if ($user && $user->id) {
                        $accessToken = $user->createToken('authToken')->accessToken;
                        if ($accessToken) {
                            $authToken = $this->saveToken($accessToken, $user->id);
                            if ($authToken) {
                                $response = response()->json([
                                    "status" => "success",
                                    "message" => "User logged in Successfully",
                                    "data" => [
                                        'id' => $user->id,
                                        'name' => $user->name,
                                        'email' => $user->email,
                                        'contact' => $user->contact,
                                        'user_type' => $user->user_type,
                                        'token' => $accessToken,
                                        'profile_url' => $user['files']
                                    ],
                                ], 200);
                            } else {
                                $response = response()->json([
                                    "error" => "Invalid credentials",
                                    "errorDetails" => "Invalid credentials",
                                    "type" => "INVALID_CREDENTIALS"
                                ], 401);
                            }
                        }
                    } else {
                        $response = response()->json([
                            "error" => "User not found",
                            "errorDetails" => "User not found",
                            "type" => "RESOURCE_NOT_FOUND"
                        ], 404);
                    }
                } else {
                    $response = response()->json([
                        "error" => "Invalid Data",
                        "errorDetails" => "Invalid Data",
                        "type" => "RESOURCE_NOT_FOUND"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "User not found",
                    "errorDetails" => "User not found",
                    "type" => "RESOURCE_NOT_FOUND"
                ], 404);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function detail($request)
    {
        $user = $this->user::where('id', $request['id'])->orderBy('id', 'DESC')->first();
        if ($user) {
            $response = response()->json([
                "status" => "success",
                "data" => $user
            ], 200);
        } else {
            $response = response()->json([
                "error" => "User not found",
                "errorDetails" => "User not aaafound",
                "type" => "RESOURCE_NOT_FOUND"
            ], 404);
        }
        return $response;
    }



    public function forgotPassword($request)
    {
        DB::beginTransaction();
        try {
            $email = $request['email'];
            $forgot_password_otp = UtilityService::generateOtp();
            $userUpdate = $this->user::where('email', $email)->where('status', 'ACTIVE')->where('verification_status', 'VERIFIED')->update(['forgot_password_otp' => $forgot_password_otp]);
            if ($userUpdate) {
                $request['verification_otp'] = $forgot_password_otp;
                $this->sendOtp($request);
                $response = response()->json([
                    "status" => "success",
                    "message" => "Forgot Password OTP sent"
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "Something went wrong",
                    "errorDetails" => "Something went wrong, please try again",
                    "type" => "RESOURCE_NOT_FOUND"
                ], 500);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function forgotPasswordUpdate($request)
    {
        DB::beginTransaction();
        try {
            $email = $request['email'];
            $getUser = $this->user::where('email', $email)->first();
            if ($getUser) {
                $password = md5($request['password']);
                $updatePassword = $this->user::where('email', $email)->update([
                    'password' => $password
                ]);
                if ($updatePassword) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "Password updated successfully"
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "Something went wrong, please try again",
                        "errorDetails" => "Something went wrong, please try again",
                        "type" => "RESOURCE_NOT_FOUND"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "Wrong OTP entered",
                    "errorDetails" => "Wrong OTP entered",
                    "type" => "RESOURCE_NOT_FOUND"
                ], 404);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function logoutUser($request)
    {
        $token = $this->getBearerToken($request);
        DB::beginTransaction();
        try {
            $token = Token::where([['token', $token]])->delete();
            if ($token) {
                $response = response()->json([
                    "status" => "success",
                    "message" => "User Logout successfully"
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "User Logout failed",
                    "errorDetails" => "User logout failed, Please try again",
                    "type" => "RESOURCE_CREATION_FAILED"
                ], 404);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function self($request)
    {
        $token = $this->getBearerToken($request);
        $token = Token::where('token', $token)->first();
        if ($token && $token->id) {
            $userData = $this->user::with('files')->where('id', $token->user_id)->where('status', 'ACTIVE')->first();
            if ($userData->id) {
                $response = response()->json([
                    "status" => "success",
                    "message" => "User logged in",
                    "data" => [
                        'id' => $userData->id,
                        'name' => $userData->name,
                        'email' => $userData->email,
                        'contact' => $userData->contact,
                        'user_type' => $userData->user_type,
                        'profile_url' => $userData['files']
                    ],
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "User not found",
                    "errorDetails" => "User not found",
                    "type" => "RESOURCE_NOT_FOUND"
                ], 404);
            }
        } else {
            $response = response()->json([
                "error" => "Token not found",
                "errorDetails" => "Token not found",
                "type" => "RESOURCE_NOT_FOUND"
            ], 401);
        }

        return $response;
    }

    public function updatePassword($request, $input)
    {
        DB::beginTransaction();
        try {
            $request['currentPassword'] = array_key_exists('currentPassword', $request) ? md5($request['currentPassword']) : '';
            $finRequest['password'] = array_key_exists('newPassword', $request) ? md5($request['newPassword']) : '';
            $user = $this->user::where('id', $input['user_id'])
                // ->where('password', $request['currentPassword'])
                ->update($finRequest);
            if ($user) {
                $response = response()->json([
                    "status" => "success",
                    "message" => "User password updated successfully"
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "User updation failed",
                    "errorDetails" => "User password updation failed, Wrong old password provided",
                    "type" => "RESOURCE_UPDATING_FAILED"
                ], 404);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function saveToken($accessToken, $id)
    {
        $token = new Token();
        $token->user_id = $id;
        $token->token = $accessToken;
        $genToken = $token->save();

        if ($genToken) {
            return $accessToken;
        } else {
            return false;
        }
    }

    public function getBearerToken($request)
    {
        $header = $request->header('Authorization', '');
        return (Str::startsWith($header, 'Bearer ')) ? Str::substr($header, 7) : false;
    }
    public function verifyOtp($request)
    {
        $user = $this->user::where('email', $request['email'])->first();
        if ($user) {
            if ($user->verification_otp === $request['otp']) {
                $result = $this->user::where('email', $request['email'])
                    ->update([
                        'verification_status' => "VERIFIED"
                    ]);

                if ($result) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "Otp verified succesfully",
                        "data" => [
                            "message" => "Otp verified succesfully"
                        ]
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "Something went wrong",
                        "errorDetails" => "Otp verification failed.. try again !!"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "Invalid Otp",
                    "errorDetails" => "Invalid Otp.. try again !!"
                ], 500);
            }
        } else {
            $response = response()->json([
                "error" => "User not found",
                "errorDetails" => "User not registered with us",
                "type" => "RESOURCE_NOT_FOUND"
            ], 404);
        }
        return $response;
    }
    public function verifyForgotPasswordOtp($request)
    {
        $user = $this->user::where('email', $request['email'])->first();
        if ($user) {
            if ($user->forgot_password_otp === $request['otp']) {
                $result = $this->user::where('email', $request['email'])
                    ->update([
                        'verification_status' => "VERIFIED"
                    ]);
                if ($result) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "Otp verified succesfully"
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "Something went wrong",
                        "message" => "Otp verification failed.. try again !!"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "Invalid Otp",
                    "message" => "Otp verification failed.. try again !!"
                ], 500);
            }
        } else {
            $response = response()->json([
                "error" => "User not found",
                "errorDetails" => "User not registered with us",
                "type" => "RESOURCE_NOT_FOUND"
            ], 404);
        }
        return $response;
    }
    public function sendOtp($request)
    {
        $data = ['subject' => 'Verification OTP', 'otp' => $request['verification_otp']];

        try {
            @Mail::to($request['email'])->send(new SendOtpMail($data));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function resendOtp($request)
    {
        $data = ['subject' => 'Verification OTP', 'otp' => UtilityService::generateOtp()];

        try {
            @Mail::to($request['email'])->send(new SendOtpMail($data));
            $user = $this->user::where('email', $request['email'])
                ->update(["verification_otp" => $data['otp']]);
            if ($user) {
                $response = response()->json([
                    "status" => "success",
                    "message" => "Otp sent successfully.."
                ], 200);
            } else {
                $response = response()->json([
                    "error" => "Something went wrong",
                    "errorDetails" => "Otp not sent.. try again !!",
                    "type" => "RESOURCE_UPDATION_FAILED"
                ], 500);
            }
        } catch (\Exception $e) {
            $response = response()->json([
                "error" => "Something went wrong",
                "errorDetails" => "Otp not sent.. try again !!",
                "type" => "RESOURCE_CREATION_FAILED"
            ], 500);
        }
        return $response;
    }
}
