$(document).ready(function () {
    $(".taskSubmit").click(submitForm);
});
let taskId = "";
function validateForm() {
    var isValid = true;

    var title = $("#title").val();
    if (title === "") {
        $("#title_error").html("Title is required");
        isValid = false;
    } else {
        $("#title_error").html("");
    }

    var priority = $("#priority").val();
    if (priority === "") {
        $("#priority_error").html("Priority is required");
        isValid = false;
    } else {
        $("#priority_error").html("");
    }

    return isValid;
}

function submitForm() {
    if (!validateForm()) {
        return;
    }
    const project_id = $("#selected_project_id").val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    var formDataArray = $("#taskForm").serializeArray();

    var formDataObject = {};
    formDataArray.forEach(function (item) {
        formDataObject[item.name] = item.value;
    });
    formDataObject["project_id"] = project_id;

    var formDataJSON = JSON.stringify(formDataObject);

    $.ajax({
        url: "/task",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
        data: formDataJSON,
        success: function (response) {
            $("#taskForm")[0].reset();
            $("#success_msg").text(response.message);
            setTimeout(() => {
                $("#success_msg").text("");
            }, 2000);
            getAllTask();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}

function getAllProjects() {
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url: "/project", // Endpoint for getting all projects
        type: "GET",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
        success: function (response) {
            console.log(response);
            var html = "";
            html += `<select class="form-select" id="selected_project_id" onChange="getAllTask();"
        style="border: none;box-shadow: inset -5px 2px 5px rgb(255 255 255 / 20%);">`;
            response.data.forEach(function (project) {
                html += `<option value="${project["id"]}">${project["name"]}</option>`;
            });
            html += `</select>`;
            $("#project_list").html(html);
            getAllTask();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}
getAllProjects();

// Define the renderTodoList function
function renderTodoList(data) {
    var html = "";
    data.data.forEach(function (task) {
        var priorityClass = "";
        var priorityText = "";
        if (task.priority === "0") {
            priorityClass = "low-priority";
            priorityText = "Low";
        } else if (task.priority === "1") {
            priorityClass = "medium-priority";
            priorityText = "Medium";
        } else if (task.priority === "2") {
            priorityClass = "high-priority";
            priorityText = "High";
        }

        // Parse created_at date string
        var createdAtDate = new Date(task.created_at);
        // Format date as 'YYYY-MM-DD'
        var formattedDate =
            createdAtDate.getFullYear() +
            "-" +
            (createdAtDate.getMonth() + 1).toString().padStart(2, "0") +
            "-" +
            createdAtDate.getDate().toString().padStart(2, "0");

        var taskHtml =
            '<div class="card task-card my-2">' +
            '<div class="card-body ">' +
            "<div class='d-flex justify-content-between'><h5 data-title='" +
            task.title +
            "' data-id='" +
            task.id +
            "' data-priority='" +
            task.priority +
            "'>" +
            task.title +
            "</h5><div style='height:100%'><i class='fa-solid fa-pen-to-square edit-icon' data-bs-toggle='modal' data-bs-target='#exampleModal' style='font-size:15px'></i><i class='fa-solid fa-trash delete-icon' style='font-size:15px'></i></div></div><hr>" +
            '<div class="bottom-element">' +
            "<span> <i class='fas fa-clock mx-1'></i>" +
            formattedDate +
            "</span>" +
            '<span class="' +
            priorityClass +
            '">' +
            priorityText +
            "</span>" +
            "</div>" +
            "</div>" +
            "</div>";
        html += taskHtml;
    });
    $("#todoList").html(html);
}
$(document).on("click", ".edit-icon", function () {
    var title = $(this).closest(".card-body").find("h5").attr("data-title");
    var id = $(this).closest(".card-body").find("h5").attr("data-id");
    var priority = $(this)
        .closest(".card-body")
        .find("h5")
        .attr("data-priority");
    console.log("Title: " + title + ", ID: " + id + ", Priority: " + priority);
    updateFormData(title, id, priority);
});

$(document).on("click", ".delete-icon", function () {
    var id = $(this).closest(".card-body").find("h5").attr("data-id");
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    $.ajax({
        url: "/task/" + id,
        type: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
        success: function (response) {
            alert(response.message);
            getAllTask();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
});

function updateFormData(title, id, priority) {
    $("#title").val(title);
    $("#priority").val(priority);
    $("#formTitle").text("Update");
    $("#submitForm").addClass("d-none");
    $("#updateForm").removeClass("d-none");
    taskId = id;
    console.log(taskId);
}

// Define the getAllTask function
function getAllTask() {
    const project_id = $("#selected_project_id").val();
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    $.ajax({
        url: "/task",
        type: "GET",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
        data: {
            project_id: project_id,
        },
        success: function (response) {
            console.log(response);
            renderTodoList(response);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
}

// Call the getAllTask function

// Call the function to update the project list

$(".taskSubmit").submit(function (event) {
    console.log($(".taskForm").serializeArray());
    // Prevent default form submission
    event.preventDefault();

    // Get CSRF token from the meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr("content");

    // Get form data
    var formData = $(this).serialize();

    // Send AJAX POST request to add task
    $.ajax({
        url: "/task",
        type: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        data: formData,
        success: function (response) {
            // Handle success response
            console.log(response);
            // Optionally, update UI or show success message
        },
        error: function (xhr, status, error) {
            // Handle error response
            console.error(xhr.responseText);
            // Optionally, display error message to the user
        },
    });
});
$(".updateForm").click(function (event) {
    if (!validateForm()) {
        return;
    }

    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    let formDataObject = {};
    formDataObject["title"] = $("#title").val();
    formDataObject["priority"] = $("#priority").val();

    var formDataJSON = JSON.stringify(formDataObject);
    $.ajax({
        url: "/task/" + taskId,
        type: "PUT",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
        },
        data: formDataJSON,
        success: function (response) {
            $("#taskForm")[0].reset();
            $("#success_msg").text(response.message);
            getAllTask();
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });

    // Prevent the default form submission
    event.preventDefault();
});
