$(function(){

    $("body")
        //Изменение даты на календаре
        .on("change", ".schedule_calendar", function(){
            loaderOn();
            var date = $(this).val();
            var userid = $("#userid").val();
            getSchedule(userid, date, loaderOff);
        })
        //Открытие окна с выставлением периода отсутствия
        .on("click", ".schedule_absent", function(e){
            e.preventDefault();
            var clientid = $(this).parent().parent().data("clientid");
            getScheduleAbsentPopup(clientid);
        })
        //Сохранение периода отсутствия
        .on("click", ".popop_schedule_absent_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("../admin?menuTab=Main&menuAction=updateAction&ajax=1", refreshSchedule);
        })
        //Окно добавления урока в расписание
        .on("click", ".add_lesson", function(){
            var type = $(this).data("schedule_type");
            var class_id = $(this).data("class_id");
            var date = $(this).data("date");
            var area_id = $(this).data("area_id");
            getScheduleLessonPopup(class_id, date, area_id, type);
        })
        //Сохранение урока в расписание
        .on("click", ".popop_schedule_lesson_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("../admin?menuTab=Main&menuAction=updateAction&ajax=1", refreshSchedule);
        })
        //Удаление урока из основного графика
        .on("click", ".schedule_delete_main", function(e){
            e.preventDefault();
            loaderOn();
            var lessonid = $(this).data("id");
            var deletedate = $(this).data("date");
            markDeleted(lessonid, deletedate, refreshSchedule);
        })
        //Выставление отсутствия занятия
        .on("click", ".schedule_today_absent", function(e){
            e.preventDefault();
            loaderOn();
            var lessonid = $(this).parent().parent().data("id");
            var date = $(this).parent().parent().data("date");
            var type = $(this).parent().parent().data("type");

            if(type == "Schedule_Lesson")
                markAbsent(lessonid, date, refreshSchedule);
            if(type == "Schedule_Current_Lesson")
                deleteItem(type, lessonid, "../admin?menuTab=Main&menuAction=deleteAction&ajax=1", refreshSchedule);

            loaderOff();
        })
        .on("click", ".schedule_update_time", function(e){
            e.preventDefault();
            //loaderOn();
            var lessonid = $(this).parent().parent().data("id");
            var date = $(this).parent().parent().data("date");
            var type = $(this).parent().parent().data("type");
            getScheduleChangeTimePopup(lessonid, type, date);
        })
        .on("click", ".popop_schedule_time_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("../admin?menuTab=Main&menuAction=updateAction&ajax=1", refreshSchedule);
        });


    var today = new Date();
    var day =   today.getDate();
    var month = today.getMonth() + 1;
    var year =  today.getFullYear();

    if(day < 10)    day = "0" + day;
    if(month < 10)  month = "0" + month;

    var result = year + "-" + month + "-" + day;
    $(".schedule_calendar").val(result);

});


function refreshSchedule() {
    $(".schedule_calendar").trigger("change");
}


function getSchedule(userid, date, func) {
    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "getSchedule",
            userid: userid,
            date: date,
        },
        success: function(responce){
            $(".schedule").empty();
            $(".schedule").append(responce);
            func();
            loaderOff();
        }
    });
}


function getScheduleAbsentPopup(clientid) {
    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "getScheduleAbsentPopup",
            client_id: clientid,
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function getScheduleLessonPopup(class_id, date, area_id, type) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "getScheduleLessonPopup",
            class_id: class_id,
            date: date,
            model_name: type,
            area_id: area_id
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}

function markDeleted(lessonid, deletedate, func) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "markDeleted",
            lessonid: lessonid,
            deletedate: deletedate
        },
        success: function(responce){
            //alert(responce);
            func();
        }
    });
}

function markAbsent(lessonid, date, func) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "markAbsent",
            lessonid: lessonid,
            date: date
        },
        success: function(responce){
            func();
        }
    });
}


function changeTime(lessonid, model_name, time1, time2) {

}


function getScheduleChangeTimePopup(lessonid, model_name, date) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "getScheduleChangeTimePopup",
            id: lessonid,
            type: model_name,
            date: date
        },
        success: function(responce){
            showPopup(responce)
        }
    });
}