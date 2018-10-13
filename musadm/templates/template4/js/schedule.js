
var root = "/musadm";

$(function(){

    var days = ["Воскресенье", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота"];

    $(".submenu").on("click", "a", function(e){ e.preventDefault(); });

    $("body")
        //Изменение даты на календаре
        .on("change", ".schedule_calendar", function(){
            loaderOn();
            var date = $(".schedule_calendar").val();
            console.log( date );
            var userid = $("#userid").val();
            var newDate = new Date( $(".schedule_calendar").val() );
            var dayName = days[newDate.getDay()];
            $(".day_name").text( dayName );
            getSchedule(userid, date, loaderOff);
        })
        //Открытие окна с выставлением периода отсутствия
        .on("click", ".schedule_absent", function(e){
            e.preventDefault();
            var clientid = $(this).parent().parent().data("clientid");
            var typeid = $(this).parent().parent().data("typeid");
            getScheduleAbsentPopup(clientid, typeid);
        })
        //Сохранение периода отсутствия
        .on("click", ".popop_schedule_absent_submit", function(e){
            e.preventDefault();
            loaderOn();

            if( $("#absent_add_task").is(":checked") )
            {
                var dateTo = $("input[name=dateTo]").val();
                var clientId = $("input[name=clientId]").val();
                addAbsentTask(dateTo, clientId);
            }

            saveData("Main", refreshSchedule);
        })
        //Окно добавления урока в расписание
        .on("click", ".add_lesson", function(){
            var date = $(this).data("date");
            var lessonDate = new Date(date);
            var currentDate = new Date(getCurrentDate());

            if(lessonDate.valueOf() < currentDate.valueOf())    return false;

            var type = $(this).data("schedule_type");
            var class_id = $(this).data("class_id");
            var area_id = $(this).data("area_id");
            getScheduleLessonPopup(class_id, date, area_id, type);
        })
        //Сохранение урока в расписание
        .on("click", ".popop_schedule_lesson_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", refreshSchedule);
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
            markAbsent(lessonid, date, refreshSchedule);
            loaderOff();
        })
        .on("click", ".schedule_update_time", function(e){
            e.preventDefault();
            var lessonid = $(this).parent().parent().data("id");
            var date = $(this).parent().parent().data("date");
            getScheduleChangeTimePopup(lessonid, date);
        })
        .on("click", ".popop_schedule_time_submit", function(e){
            e.preventDefault();
            loaderOn();
            var timeFrom = $("input[name=timeFrom]").val();
            var timeTo = $("input[name=timeTo]").val();
            var lessonId = $("input[name=lesson_id]").val();
            var date = $("input[name=date]").val();
            saveScheduleChangeTimePopup(lessonId, date, timeFrom, timeTo, refreshSchedule);
        })
        .on("change", "select[name=typeId]", function(){
            loaderOn();
            var type = $(this).val();
            var select = $(".clients");

            if(type != 0) select.show();
            else select.hide();

            if( type == 3 )
            {
                var select = $("select[name=clientId]");
                var selectBlock = select.parent();
                select.remove();
                selectBlock.append("<input type='number' name='clientId' class='form-control' placeholder='Номер лида' />");
                loaderOff();
            }
            else
            {
                var input = $("input[name=clientId]");
                var inputBlock = input.parent();
                input.remove();
                inputBlock.append("<select name='clientId' class='form-control valid' ></select>");

                $.ajax({
                    type: "GET",
                    url: "",
                    data: {action: "getclientList", type: type},
                    success: function(responce){
                        var select = $("select[name=clientId]");
                        select.empty();
                        select.append(responce);
                        loaderOff();
                    }
                });
            }
        })
        .on("click", ".send_report", function(e){
            e.preventDefault();
            loaderOn();
            var tr = $(this).parent().parent();
            var lesson_id = tr.find("input[name=lessonId]").val();
            var teacher_id = tr.find("input[name=teacherId]").val();
            var client_id = tr.find("input[name=clientId]").val();
            var type_id = tr.find("input[name=typeId]").val();
            var date = tr.find("input[name=date]").val();
            var lesson_type = tr.find("input[name=lessonType]").val();
            var attendance = tr.find("input[name=attendance]").is(':checked');
            if(attendance == true) attendance = 1;
            else attendance = 0;

            $.ajax({
                type: "GET",
                url: "",
                data: {
                    action: "teacherReport",
                    teacher_id: teacher_id,
                    client_id: client_id,
                    type_id: type_id,
                    date: date,
                    lesson_id: lesson_id,
                    lesson_type: lesson_type,
                    attendance: attendance
                },
                success: function(responce){
                    if(responce != "0") alert(responce);
                    refreshSchedule();
                }
            });
        })
        .on("click", ".delete_report", function(e){
            e.preventDefault();
            loaderOn();
            var tr = $(this).parent().parent();
            var id = tr.find("input[name=reportId]").val();
            var lessonId = tr.find("input[name=lessonId]").val();
            var lessonType = tr.find("input[name=lessonType]").val();

            $.ajax({
                type: "GET",
                url: "",
                data: {
                    action: "deleteReport",
                    lesson_id: lessonId,
                    lesson_type: lessonType,
                    report_id: id,
                },
                success: function(responce) {
                    if(responce != "0") alert(responce);
                    refreshSchedule();
                }
            });
        })
        .on("click", ".schedule_task_create", function(){
            newScheduleTaskPopup();
        })
        .on("click", ".popop_schedule_task_submit", function(e){
            e.preventDefault();
            loaderOn();
            var form = $("#createData");

            if(form.valid())
            {
                var formData = form.serialize();
                saveScheduleTask(formData, loaderOff);
            }
            else
            {
                loaderOff();
            }
        })
        .on("click", ".add_teacher_payment", function(e){
            e.preventDefault();
            loaderOn();
            var date = $(".teacher_payments").find("input[name=date]").val();
            var summ = $(".teacher_payments").find("input[name=summ]").val();
            var user = $(".teacher_payments").find("input[name=userid]").val();
            saveTeacherPayment(user, summ, date, "Выплата преподавателю", refreshSchedule);
        })
        .on("change", ".client_schedule", function(){
            loaderOn();

            var month = $("#month").val();
            var year = $("#year").val();
            var userid = $("#userid").val();

            $.ajax({
                type: "GET",
                url: "",
                data: {
                    ajax: 1,
                    year: year,
                    month: month,
                    userid: userid,
                },
                success: function(responce){
                    $(".users").empty();
                    $(".users").html(responce);
                    loaderOff();
                }
            });
        })
        .on("click", ".schedule_area_edit", function(e){
            e.preventDefault();
            var areaId = $(this).data("area_id");
            getScheduleAreaPopup(areaId);
        })
        .on("click", ".popop_schedule_area_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", refreshAreasTable);
        })
        .on("click", "input[name=schedule_area_active]", function(){
            var areaId = $(this).data("area_id");
            var value = $(this).prop("checked");
            updateActive("Schedule_Area", areaId, value, loaderOff);
        })
        .on("click", ".schedule_area_delete", function(e){
            e.preventDefault();
            loaderOn();
            var areaId = $(this).data("area_id");
            deleteItem("Schedule_Area", areaId, refreshAreasTable);
        });


    var today = new Date();
    var day =   today.getDate();
    var month = today.getMonth() + 1;
    var year =  today.getFullYear();

    $(".day_name").text( days[today.getDay()] );

    if(day < 10)    day = "0" + day;
    if(month < 10)  month = "0" + month;

    var result = year + "-" + month + "-" + day;
    $(".schedule_calendar").val(result);

});



function refreshAreasTable() {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "getSchedule",
        },
        success: function(responce) {
            $(".page").html(responce);
            loaderOff();
        }
    });
}


function saveTeacherPayment(user, summ, date, description, func) {
    $.ajax({
        type: "GET",
        url: root + "/admin?menuTab=Main&menuAction=updateAction&ajax=1",
        async: false,
        data: {
            id: "",
            modelName: "Payment",
            user: user,
            value: summ,
            type: 3,
            datetime: date,
            description: description
        },
        success: function(responce){
            if(responce != "0") alert("Ошибка: " + responce);
            closePopup();
            func();
        }
    });
}


function getScheduleAreaPopup(areaId) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "getScheduleAreaPopup",
            areaId: areaId
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function newScheduleTaskPopup() {
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


function saveScheduleTask(formData, func) {
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


function refreshSchedule() {
    $(".schedule_calendar").trigger("change");
    $("#month").trigger("change");
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


function getScheduleAbsentPopup(clientid, typeid) {
    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "getScheduleAbsentPopup",
            client_id: clientid,
            type_id: typeid
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
            $("select[name=typeId]").val("1");
            $("select[name=typeId]").trigger("change");
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


function getScheduleChangeTimePopup(lessonid, date) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "getScheduleChangeTimePopup",
            id: lessonid,
            date: date
        },
        success: function(responce){
            showPopup(responce)
        }
    });
}


function saveScheduleChangeTimePopup(lessonId, date, timeFrom, timeTo, func) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "saveScheduleChangeTimePopup",
            lesson_id: lessonId,
            date: date,
            time_from: timeFrom,
            time_to: timeTo
        },
        success: function(responce){
            if(responce != "")  alert(responce);
            closePopup();
            func();
        }
    });
}


function addAbsentTask(dateTo, clientId) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "addAbsentTask",
            date_to: dateTo,
            client_id: clientId
        },
        success: function(responce){
            if(responce != "")  alert(responce);
        }
    });
}