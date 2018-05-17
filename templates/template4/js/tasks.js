$(function(){
    $("body")
        .on("click", ".task_create", function(e){
            e.preventDefault();
            newTaskPopup();
        })
        .on("change", "select[name=type]", function(e){
            var type = $(this).val();
            var dateRow = $(".date");
            var dateInp = $("input[name=date]");

            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate();

            if(month < 10)  month = "0" + month;
            if(day < 10)    day = "0" + day;

            var currentDate = year + "-" + month + "-" + day;
            dateInp.val(currentDate);

            if(type == 3)
            {
                dateRow.show("slow");
            }
            else
            {
                dateRow.hide("slow");
            }
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
            //$(this).parent().empty();
            $(this).parent().find("input[type=date]").remove();
            $(this).parent().append("<span>"+taskDate+"</span>");
            $(this).parent().append("<a href='#' class='action edit task_date_edit' data-task_id='"+taskId+"'></a>");
            $(this).remove();
        });
});


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


function refreshTableAll() {
    $.ajax({
        type: "GET",
        url: "all",
        data: {
            action: "refresh_table"
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
            action: "new_task_popup"
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