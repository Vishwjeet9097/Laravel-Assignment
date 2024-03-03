<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Managment System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>

<body style="background:antiquewhite">

    <div class="container">
        <div class="row my-3 justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <h5 class="text-center my-4 header">Task Managment</h5>

                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between custom-card-header">
                        <div class="" id="project_list"></div>
                        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Create</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card todo-card">
                                    <div class="card-header heading">ToDo:</div>
                                    <div class="card-body" id="todoList">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formTitle">Create Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        <div class="row mx-3">
                            <h5 id="success_msg" class="text-success"></h5>
                            <hr>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title"
                                    placeholder="Enter here..">
                                <div class="error-message text-danger" id="title_error"></div>
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Priority</label>
                                <select class="form-select" name="priority" id="priority">
                                    <option value="" selected>Select Priority</option>
                                    <option value="2">High</option>
                                    <option value="1">Medium</option>
                                    <option value="0">Low</option>
                                </select>
                                <div class="error-message text-danger" id="priority_error"></div>
                            </div>
                            <!-- <div class="mb-3">
                                <button type="button" id="submitForm" class="btn btn-outline-success w-100"
                                    onclick="submitForm();">Submit</button>
                                <button type="button" id="updateForm"
                                    class="btn btn-outline-success w-100 d-none updateForm">Update</button>
                            </div> -->
                            <div class="mb-3">
                                <button type="button" id="submitForm"
                                    class="btn btn-outline-success w-100 taskSubmit">Submit</button>
                                <button type="button" id="updateForm"
                                    class="btn btn-outline-success w-100 d-none updateForm">Update</button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{asset('js/script.js')}}"></script>

</html>