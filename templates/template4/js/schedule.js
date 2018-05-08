$(function(){

    $("body")
        .on("change", ".schedule_calendar", function(){
            loaderOn();
            var date = $(this).val();
            var userid = $("#userid").val();
            getSchedule(userid, date, loaderOff);
        })
        .on("click", ".schedule_absent", function(e){
            e.preventDefault();
            var clientid = $(this).parent().parent().data("clientid");
            getScheduleAbsentPopup(clientid);
            //showPopup("<h2>Hello</h2>");
        })
        .on("click", ".popop_schedule_absent_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("../admin?menuTab=Main&menuAction=updateAction&ajax=1", loaderOff);
        })
        .on("click", ".add_lesson", function(){
            var type = $(this).data("schedule_type");
            var class_id = $(this).data("class_id");
            var date = $(this).data("date");
            var area_id = $(this).data("area_id");
            getScheduleLessonPopup(class_id, date, area_id, type);
        })
        .on("click", ".popop_schedule_lesson_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("../admin?menuTab=Main&menuAction=updateAction&ajax=1", refreshSchedule);
        });


    var today = new Date();
    var day = today.getDate();
    var month = today.getMonth() + 1;
    var year = today.getFullYear();

    if(day < 10)    day = "0" + day;
    if(month < 10)  month = "0" + month;

    var result = year + "-" + month + "-" + day;
    $(".schedule_calendar").val(result);

});


function refreshSchedule() {
    $(".schedule_calendar").trigger("change");
    loaderOff();
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