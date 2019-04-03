'use strict';
var root = $('#rootdir').val();
var taskAfterActionValue = $('#taskAfterAction').val();

$(function() {
    $('body')
        .on('click', '.popop_task_submit', function(e) {
            e.preventDefault();
            loaderOn();
            var form = $('#createData');
            if (form.valid()) {
                var formData = form.serialize();
                saveTask(formData, refreshTasksTable);
            } else {
                loaderOff();
            }
        });
});


function addTaskNotePopup(taskId) {
    var popupData = "" +
        "<form name=\"createData\" id=\"createData\" action=\".\" novalidate=\"novalidate\">" +
        "<div class=\"column\"><span>Текст задачи</span><span style=\"color:red\">*</span></div>" +
        "<div class=\"column\"><input type=\"text\" required name=\"text\" class=\"form-control\"></div>" +
        "<input type='hidden' name='id' value=''>" +
        "<input type='hidden' name='modelName' value='Task_Note'>" +
        "<input type='hidden' name='taskId' value='"+taskId+"'>" +
        "<button class=\"btn btn-default\" onclick='loaderOn();saveData(\"Main\", function(response){taskAfterAction();loaderOff();})'>Сохранить</button>" +
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
            break;
        default: loaderOff();
    }
}


function changeTaskPriority(taskId, priorityId, func) {
    $.ajax({
        type: 'GET',
        url: root + '/tasks',
        dataType: 'json',
        data: {
            action: 'changeTaskPriority',
            taskId: taskId,
            priorityId: priorityId
        },
        success: function (response) {
            func(response);
            notificationSuccess('Приоритет задачи №' + response.taskId + ' изменен на ' + response.priorityTitle);
        },
        error: function (response) {
            notificationError('При изменении приоритета задачи произошла ошибка');
            loaderOff();
        }
    });
}


function assignmentTaskPopup(taskId) {
    $.ajax({
        url: root + '/tasks',
        type: 'GET',
        data: {
            action: 'task_assignment_popup',
            taskId: taskId
        },
        success: function(response){
            showPopup(response);
        }
    });
}


function markAsDone(taskId, callBack) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/tasks',
        data: {
            action: 'markAsDone',
            taskId: taskId
        },
        success: function(response) {
            if (response != '0') {
                notificationError('Ошибка: ' + response);
                return;
            }
            callBack(response);
            loaderOff();
        }
    });
}


function updateTaskDate(taskId, taskDate) {
    $.ajax({
        type: 'GET',
        url: root + '/tasks',
        data: {
            action: 'update_date',
            taskId: taskId,
            date: taskDate
        },
        success: function(response) {

        }
    });
}


function updateTaskArea(taskId, areaId) {
    $.ajax({
        type: 'GET',
        url: root + '/tasks',
        data: {
            action: 'update_area',
            taskId: taskId,
            areaId: areaId
        },
        success: function(response) {

        }
    });
}


function refreshTasksTable(from, to) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'refreshTasksTable',
            date_from: from,
            date_to: to
        },
        success: function(response) {
            $('.tasks').html(response);
            loaderOff();
        }
    });
}


function newTaskPopup() {
    $.ajax({
        type: 'GET',
        url: root + '/tasks',
        data: {
            action: 'new_task_popup',
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


function saveTask(formData, callBack) {
    formData += '&action=save_task';

    $.ajax({
        type: 'GET',
        url: root + '/tasks',
        data: formData,
        success: function(response) {
            if (response != '0') {
                notificationError(response);
            }
            closePopup();
            callBack();
            loaderOff();
        }
    });
}