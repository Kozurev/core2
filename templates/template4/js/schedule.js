$(function(){

    $("body")
        .on("change", ".schedule_calendar", function(){
            loaderOn();
            var date = $(this).val();
            var userid = $("#userid").val();
            //var areaid = $("#areaid").val();
            getSchedule(userid, date, loaderOff);
        });


    var today = new Date();
    var day = today.getDate();
    var month = today.getMonth() + 1;
    var year = today.getFullYear();

    if(day < 10)    day = "0" + day;
    if(month < 10)  month = "0" + month;

    var result = year + "-" + month + "-" + day;
    $(".schedule_calendar").val(result);
    //$(".schedule_calendar").trigger("change");
    //$(".schedule_calendar").change();

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