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


function getSchedule(userid, date, func) {
    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "getSchedule",
            userid: userid,
            date: date,
            //area: areaid
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