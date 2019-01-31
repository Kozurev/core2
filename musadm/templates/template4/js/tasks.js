var root = $("#rootdir").val();
var taskAfterActionValue = $("#taskAfterAction").val();


$(function(){
    $("body")
        .on("click", ".task_create", function(e){
            e.preventDefault();
            newTaskPopup();
        })
        .on("click", ".popop_task_submit", function(e){
            e.preventDefault();
            loaderOn();
            var form = $("#createData");

            if(form.valid())
            {
                var formData = form.serialize();
                saveTask(formData, refreshTasksTable);
            }
            else
            {
                loaderOff();
            }
        })
        // .on("click", ".task_date_edit", function(e){
        //     e.preventDefault();
        //     var taskId = $(this).data("task_id");
        //
        //     var taskDate = $(this).parent().find("span");
        //     var taskDateVal = taskDate.text();
        //     var taskDay = taskDateVal.substr(0, 2);
        //     var taskMonth = taskDateVal.substr(3, 2);
        //     var taskYear = taskDateVal.substr(6, 4);
        //     taskDateVal = taskYear + "-" + taskMonth + "-" + taskDay;
        //
        //     taskDate.remove();
        //     $(this).parent().append("<input type='date' value='" + taskDateVal + "' class='form-control'> ");
        //     $(this).parent().append("<a href='#' class='action save save_task_date' data-task_id='" + taskId + "' title='Сохранить изменения'></a>");
        //     $(this).remove();
        // })
        // .on("click", ".save_task_date", function(e){
        //     e.preventDefault();
        //     var taskId = $(this).data("task_id");
        //     var taskDate = $(this).parent().find("input[type=date]").val();
        //     updateTaskDate(taskId, taskDate);
        //
        //     var taskYear = taskDate.substr(0, 4);
        //     var taskMonth = taskDate.substr(5, 2);
        //     var taskDay = taskDate.substr(8, 2);
        //     var taskDateVal = taskDay + "." + taskMonth + "." + taskYear;
        //
        //     $(this).parent().find("input[type=date]").remove();
        //     $(this).parent().append("<span>"+taskDateVal+"</span>");
        //     $(this).parent().append("<a href='#' class='action edit task_date_edit' data-task_id='"+taskId+"'></a>");
        //     $(this).remove();
        // })
        .on("change", ".task_date", function (e) {
            e.preventDefault();
            var taskId = $(this).data("taskid");
            var date = $(this).val();
            updateTaskDate(taskId, date);
        })
        .on("change", ".task_area", function(e){
            var taskId = $(this).data("taskid");
            var areaId = $(this).val();
            updateTaskArea(taskId, areaId);
        })
        .on("click", ".task_append_done", function(e){
            e.preventDefault();
            loaderOn();
            var task_id = $(this).data("task_id");
            markAsDone(task_id, function(response){
                taskAfterAction();
            });
        })
        .on("click", ".task_add_note", function(e){
            e.preventDefault();
            var task_id = $(this).data("task_id");
            addTaskNotePopup(task_id);
        })
        .on("click", ".popop_task_note_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", function(response){taskAfterAction();});
        })
        .on("click", ".tasks_show", function(){
            loaderOn();
            var dateFrom = $("input[name=date_from]").val();
            var dateTo = $("input[name=date_to]").val();
            refreshTasksTable(dateFrom, dateTo);
        })
        .on("click", ".associate", function(e){
            e.preventDefault();
            assignmentTaskPopup( $(this).data("task_id") );
        })
        .on("click", ".popop_task_assignment_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", function(response){taskAfterAction();});
        })
        .on("change", "input[data-type=task_priority]", function(e){
            var priorityId = $(this).val();
            var taskId = $(this).data("taskid");
            var cardSize = $(this).data("card-size");

            loaderOn();
            changeTaskPriority(taskId, priorityId, cardSize, function(response){
                // $(".tasks").html(response);
                // loaderOff();
                taskAfterAction();
            });
        });

});


function addTaskNotePopup(task_id) {
    var popupData = "" +
        "<form name=\"createData\" id=\"createData\" action=\".\" novalidate=\"novalidate\">" +
        "<div class=\"column\"><span>Текст задачи</span><span style=\"color:red\">*</span></div>" +
        "<div class=\"column\"><input type=\"text\" required name=\"text\" class=\"form-control\"></div>" +
        "<input type='hidden' name='id' value=''>" +
        "<input type='hidden' name='modelName' value='Task_Note'>" +
        "<input type='hidden' name='taskId' value='"+task_id+"'>" +
        "<button class=\"popop_task_note_submit btn btn-default\">Сохранить</button>" +
        "</form>";

    showPopup(popupData);
}


function taskAfterAction() {
    loaderOn();

    switch(taskAfterActionValue)
    {
        case 'balance':
            var userId = $("#userid").val();
            refreshPaymentsTable(userId, loaderOff);
            break;
        case 'tasks':
            refreshTasksTable();
    }
}


function changeTaskPriority(taskId, priorityId, cardSize, func) {
    $.ajax({
        type: "GET",
        url: root + "/tasks",
        data: {
            action: "changeTaskPriority",
            task_id: taskId,
            priority_id: priorityId,
            card_size: cardSize
        },
        success: function(response){
            func(response);
        }
    });
}


function assignmentTaskPopup(task_id) {
    $.ajax({
        url: root + "/tasks",
        type: "GET",
        data: {
            action: "task_assignment_popup",
            taskid: task_id
        },
        success: function(response){
            showPopup(response);
        }
    });
}


function markAsDone(task_id, func) {
    $.ajax({
        type: "GET",
        url: root + "/tasks",
        data: {
            action: "markAsDone",
            task_id: task_id
        },
        success: function(responce){
            if(responce != "0")
            {
                alert(responce);
                loaderOff();
                return;
            }

            func(responce);
        }
    });
}


function updateTaskDate(taskId, taskDate) {
    $.ajax({
        type: "GET",
        url: root + "/tasks",
        data: {
            action: "update_date",
            task_id: taskId,
            date: taskDate
        },
        success: function(responce){}
    });
}


function updateTaskArea(taskId, areaId) {
    $.ajax({
        type: "GET",
        url: root + "/tasks",
        data: {
            action: "update_area",
            task_id: taskId,
            area_id: areaId
        },
        success: function(response){}
    });
}


function refreshTasksTable(from, to) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "refreshTasksTable",
            date_from: from,
            date_to: to
        },
        success: function(responce){
            $(".tasks").empty();
            $(".tasks").append(responce);
            loaderOff();
        }
    });
}


function newTaskPopup() {
    $.ajax({
        type: "GET",
        url: root + "/tasks",
        data: {
            action: "new_task_popup",
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function saveTask(formData, func) {
    formData += "&action=save_task";

    $.ajax({
        type: "GET",
        url: root + "/tasks",
        data: formData,
        success: function(responce){
            if(responce != "0") alert(responce);
            closePopup();
            func();
            loaderOff();
        }
    });
}