<?php

namespace App\Repositories;

use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
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
            if ($request['user_type'] === 'ADMIN' || $request['user_type'] === 'SUPER_ADMIN') {
                $request['username'] = $request['contact'];
                $request['password'] = md5($request['password']);
                $request['status'] = 'ACTIVE';
                $request['contact_status'] = 'VERIFIED';
                $request['user_type'] = 'USER';
                $request['added_by'] = 'ADMIN';
                $user = $this->user::create($request);
                if ($user && $user->id) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "User added successfully",
                        "data" => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'username' => $user->username,
                            'email' => $user->email,
                            'contact' => $user->contact,
                            'user_type' => $user->user_type,
                            'status' => $user->status
                        ],
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "User creation failed",
                        "errorDetails" => "User creation failed, Please try again",
                        "type" => "RESOURCE_CREATION_FAILED"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "User addition failed",
                    "errorDetails" => "User addition failed, You don't have permission to perform this action",
                    "type" => "RESOURCE_CREATION_FAILED"
                ], 401);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function get($request, $searchData)
    {
        if ($request['user_type'] === 'ADMIN' || $request['user_type'] === 'USER') {
            $users = (!array_key_exists('page', $request) || $request['page'] != -1) ?
                $this->user::where('user_type', 'USER')
                    ->where([[$searchData]])
                    ->orderBy('id', 'DESC')
                    ->paginate($request['perpage']) :
                $this->user::where('user_type', 'USER')
                    ->where([[$searchData]])
                    ->orderBy('id', 'DESC')
                    ->get();
        }

        if ($users) {
            $response = response()->json([
                "status" => "success",
                "data" => ($request['page'] != -1) ? $users : array('data' => $users)
            ], 200);
        } else {
            $response = response()->json([
                "error" => "Admins not found",
                "errorDetails" => "Admins not found",
                "type" => "RESOURCE_NOT_FOUND"
            ], 404);
        }
        return $response;
    }

    public function getCount($request)
    {
        $adminsCount = $this->user::where('user_type', 'ADMIN')
            ->count();
        return response()->json([
            "status" => "success",
            "data" => $adminsCount
        ], 200);
    }

    public function detail($request)
    {
        $admin = $this->user::with('admin_detail')
            ->where('id', $request['id'])->orderBy('id', 'DESC')->first();
        if ($admin) {
            $response = response()->json([
                "status" => "success",
                "data" => $admin
            ], 200);
        } else {
            $response = response()->json([
                "error" => "Admin not found",
                "errorDetails" => "Admin not aaafound",
                "type" => "RESOURCE_NOT_FOUND"
            ], 404);
        }
        return $response;
    }

    public function update($request, $id, $input)
    {
        DB::beginTransaction();
        try {
            if ($input['user_type'] === 'ADMIN' || $input['user_type'] === 'SUPER_ADMIN') {
                $request['username'] = $request['contact'];
                $user = $this->user::where('id', $id)
                    ->where('user_type', 'USER')
                    ->update($request);
                if ($user) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "User updated successfully"
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "User updation failed",
                        "errorDetails" => "User updation failed, Please try again",
                        "type" => "RESOURCE_CREATION_FAILED"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "Admin updation failed",
                    "errorDetails" => "Admin updation failed, You don't have permission to perform this action",
                    "type" => "RESOURCE_CREATION_FAILED"
                ], 401);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function updatePassword($request, $id, $input)
    {
        DB::beginTransaction();
        try {
            if ($input['user_type'] === 'SUPER_ADMIN') {
                $request['password'] = array_key_exists('password', $request) ? md5($request['password']) : '';
                $user = $this->user::where('id', $id)
                    ->where('user_type', 'USER')
                    ->update($request);
                if ($user) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "User password updated successfully"
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "User updation failed",
                        "errorDetails" => "User password updation failed, Please try again",
                        "type" => "RESOURCE_UPDATION_FAILED"
                    ], 404);
                }
            } else {
                $response = response()->json([
                    "error" => "User password updation failed",
                    "errorDetails" => "User password updation failed, You don't have permission to perform this action",
                    "type" => "RESOURCE_UPDATION_FAILED"
                ], 401);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function updateStatus($request, $id, $input)
    {
        DB::beginTransaction();
        try {
            if ($input['user_type'] === 'SUPER_ADMIN' || $input['user_type'] === 'ADMIN') {
                $request['status'] = array_key_exists('status', $request) ? $request['status'] : '';
                if ($id !== 1) {
                    $user = $this->user::where('id', $id)
                        ->where('user_type', 'USER')
                        ->update($request);
                    if ($user) {
                        if ($request['status'] === 'INACTIVE') {
                            Token::where([['user_id', $id]])->delete();
                            $response = response()->json([
                                "status" => "success",
                                "message" => "User status updated successfully"
                            ], 200);
                        } else {
                            $response = response()->json([
                                "status" => "success",
                                "message" => "User status updated successfully"
                            ], 200);
                        }
                    } else {
                        $response = response()->json([
                            "error" => "User updation failed",
                            "errorDetails" => "User status updation failed, Please try again",
                            "type" => "RESOURCE_UPDATING_FAILED"
                        ], 404);
                    }
                } else {
                    $response = response()->json([
                        "error" => "User status updation failed",
                        "errorDetails" => "User status updation failed, You don't have permission to perform this action",
                        "type" => "RESOURCE_UPDATING_FAILED"
                    ], 401);
                }
            } else {
                $response = response()->json([
                    "error" => "User status updation failed",
                    "errorDetails" => "User status updation failed, You don't have permission to perform this action",
                    "type" => "RESOURCE_UPDATING_FAILED"
                ], 401);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }

    public function delete($id, $request)
    {
        DB::beginTransaction();
        try {
            if ($request['user_type'] === 'SUPER_ADMIN') {
                $user = $this->user::where([['id', $id]])->delete();
                if ($user) {
                    $response = response()->json([
                        "status" => "success",
                        "message" => "User deleted successfully"
                    ], 200);
                } else {
                    $response = response()->json([
                        "error" => "User deletion failed",
                        "errorDetails" => "User deletion failed, Please try again",
                        "type" => "RESOURCE_CREATION_FAILED"
                    ], 500);
                }
            } else {
                $response = response()->json([
                    "error" => "User deletion failed",
                    "errorDetails" => "User deletion failed, You don't have permission to perform this action",
                    "type" => "RESOURCE_CREATION_FAILED"
                ], 401);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }
}
