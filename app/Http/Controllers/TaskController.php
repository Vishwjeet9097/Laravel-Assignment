<?php

namespace App\Http\Controllers;

use App\Repositories\AuthRepository;
use Illuminate\Http\Request;
use App\Http\Requests\Tasks\addTaskRequest;
use App\Repositories\TaskRepository;

class TaskController extends Controller
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function add(addTaskRequest $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->taskRepository->add($inputs);
    }
    public function get(Request $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->taskRepository->get($inputs);
    }
    public function getAllProject(Request $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->taskRepository->getAllProject($inputs);
    }

    public function delete(Request $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        return $this->taskRepository->delete($inputs);
    }

    public function update(Request $request)
    {
        $inputs = array_replace_recursive(
            $request->all(),
            $request->route()->parameters(),
            $request->header()
        );
        $reqData = $this->trimKeys($inputs, [
            "title",
            "priority",
        ]);
        return $this->taskRepository->update($reqData, $inputs);
    }

    public function trimKeys($dataArr, $exceptKeysArr)
    {
        if (
            $dataArr &&
            count($dataArr) > 0 &&
            $exceptKeysArr &&
            count($exceptKeysArr) > 0
        ) {
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
                if ($value && $value !== "") {
                    $finalArr[$keys] = $value;
                }
            }
            return $finalArr;
        } else {
            return [];
        }
    }
}
