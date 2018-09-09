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
                saveTask(formData, refreshTableAll);
            }
            else
            {
                loaderOff();
            }
        })
        .on("click", ".task_date_edit", function(e){
            e.preventDefault();
            var taskId = $(this).data("task_id");

            var taskDate = $(this).parent().find("span");
            var taskDateVal = taskDate.text();
            var taskDay = taskDateVal.substr(0, 2);
            var taskMonth = taskDateVal.substr(3, 2);
            var taskYear = taskDateVal.substr(6, 4);
            taskDateVal = taskYear + "-" + taskMonth + "-" + taskDay;

            taskDate.remove();
            $(this).parent().append("<input type='date' value='" + taskDateVal + "'> ");
            $(this).parent().append("<a href='#' class='action save save_task_date' data-task_id='" + taskId + "'></a>");
            $(this).remove();
        })
        .on("click", ".save_task_date", function(e){
            e.preventDefault();
            var taskId = $(this).data("task_id");
            var taskDate = $(this).parent().find("input[type=date]").val();
            updateTaskDate(taskId, taskDate);

            var taskYear = taskDate.substr(0, 4);
            var taskMonth = taskDate.substr(5, 2);
            var taskDay = taskDate.substr(8, 2);
            var taskDateVal = taskDay + "." + taskMonth + "." + taskYear;

            $(this).parent().find("input[type=date]").remove();
            $(this).parent().append("<span>"+taskDateVal+"</span>");
            $(this).parent().append("<a href='#' class='action edit task_date_edit' data-task_id='"+taskId+"'></a>");
            $(this).remove();
        })
        .on("click", ".task_append_done", function(e){
            e.preventDefault();
            loaderOn();
            var task_id = $(this).data("task_id");
            markAsDone(task_id, loaderOff);
        })
        .on("click", ".task_add_note", function(e){
            e.preventDefault();
            var task_id = $(this).data("task_id");
            addTaskNotePopup(task_id);
        })
        .on("click", ".popop_task_note_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", refreshTableAll);
        })
        .on("click", ".tasks_show", function(){
            loaderOn();
            var dateFrom = $("input[name=date_from]").val();
            var dateTo = $("input[name=date_to]").val();
            refreshTableAll(dateFrom, dateTo);
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


function markAsDone(task_id, func) {
    $.ajax({
        type: "GET",
        url: "",
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
            refreshTableAll();
            func();
        }
    });
}


function updateTaskDate(taskId, taskDate) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "update_date",
            task_id: taskId,
            date: taskDate
        },
        success: function(responce){}
    });
}


function refreshTableAll(from, to) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "refresh_table",
            date_from: from,
            date_to: to
        },
        success: function(responce){
            $(".page").empty();
            $(".page").append(responce);
            loaderOff();
        }
    });
}


function newTaskPopup() {
    $.ajax({
        type: "GET",
        url: "",
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
        url: "",
        data: formData,
        success: function(responce){
            if(responce != "0") alert(responce);
            closePopup();
            func();
            loaderOff();
        }
    });
}