<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\Task;
use App\Services\UtilityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskRepository
{
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function add($request)
    {
        DB::beginTransaction();
        try {
                $taskDetail = $this->task::create($request);
                if ($taskDetail && $taskDetail->id) {
                    $response = response()->json(
                        [
                            "status" => "success",
                            "message" => "Task Added Successfully..",
                        ],
                        200
                    );
                } else {
                    $response = response()->json(
                        [
                            "error" => "Task could not create",
                            "errorDetails" =>
                                "Task creation failed, Please try again",
                            "type" => "RESOURCE_CREATION_FAILED",
                        ],
                        500
                    );
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }
    public function get($request)
    {
        DB::beginTransaction();
        try {
                $tasks = $this->task::where('project_id',$request['project_id'])->orderBy('id','desc')->get();
                if ($tasks) {
                    $response = response()->json(
                        [
                            "status" => "success",
                            "data"=>$tasks
                        ],
                        200
                    );
                } else {
                    $response = response()->json(
                        [
                            "error" => "Task not found",
                            "errorDetails" =>
                                "Task not found, Please try again",
                            "type" => "RESOURCE_NOY_FOUND",
                        ],
                        500
                    );
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }
    public function update($request,$input)
    {
        DB::beginTransaction();
        try {
                $tasks = $this->task::where('id',$input['unique_id'])->orderBy('id','desc')->get();
                if ($tasks) {
                    $updateTask = $this->task::where('id',$input['unique_id'])->update($request);
                    $response = response()->json(
                        [
                            "status" => "success",
                            "message"=>"Task updated successfully.."
                        ],
                        200
                    );
                } else {
                    $response = response()->json(
                        [
                            "error" => "Task not found",
                            "errorDetails" =>
                                "Task not found, Please try again",
                            "type" => "RESOURCE_NOY_FOUND",
                        ],
                        500
                    );
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }
    public function delete($request)
    {
        DB::beginTransaction();
        try {
                $tasks = $this->task::where('id',$request['unique_id'])->orderBy('id','desc')->get();
                if ($tasks) {
                    $updateTask = $this->task::where('id',$request['unique_id'])->delete();
                    $response = response()->json(
                        [
                            "status" => "success",
                            "message"=>"Task deleted successfully.."
                        ],
                        200
                    );
                } else {
                    $response = response()->json(
                        [
                            "error" => "Task not found",
                            "errorDetails" =>
                                "Task not found, Please try again",
                            "type" => "RESOURCE_NOY_FOUND",
                        ],
                        500
                    );
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }
    public function getAllProject($request)
    {
        DB::beginTransaction();
        try {
                $projects = Project::orderBy('id','desc')->get();
                if ($projects) {
                    $response = response()->json(
                        [
                            "status" => "success",
                            "data"=>$projects
                        ],
                        200
                    );
                } else {
                    $response = response()->json(
                        [
                            "error" => "Task not found",
                            "errorDetails" =>
                                "Task not found, Please try again",
                            "type" => "RESOURCE_NOY_FOUND",
                        ],
                        500
                    );
                }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $response;
    }
}