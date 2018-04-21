$(function(){
    $("body")
        //Открытие всплывающего окна редактирования пользователя
        .on("click", ".user_edit", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            var usergroupid = $(this).data("usergroup");

            if(usergroupid == 5)
                getClientPopup(userid);
            else
                getTeacherPopup(userid);
        })
        //Сохранение данных пользователя
        .on("click", ".user_edit_submit", function(e){
            e.preventDefault();
            var form = $("#createData");
            if(form.valid() == false)	return;
            var data = form.serialize();
            var aUnchecked = form.find("input[type=checkbox]:unchecked");
            for (var i = 0; i < aUnchecked.length; i++) {
                data += "&" + $(aUnchecked[i]).attr("name") + "=0";
            }
            saveUserData(data);
        })
        //Добавление пользователя в архив
        .on("click", ".user_archive", function(){
            var agree = confirm("Перенести пользователя в архив?");
            if(agree != true) return;
            var userid = $(this).data("userid");
            changeUserActive(userid, "false");
        })
        //"Разархивирование пользователя"
        .on("click", ".user_unarchive", function(){
            var agree = confirm("Убрать пользователя из архива?");
            if(agree != true) return;
            var userid = $(this).data("userid");
            changeUserActive(userid, "true");
        })
        //Нажатие на кнопку закрытия высплывающего окна редактирования пользователя
        .on("click", ".popup_close", function(e){
            e.preventDefault();
            closePopup();
        });
});


function showPopup(data) {
    $(".overlay").show();
    $(".popup").empty();
    $(".popup").append('<a href="#" class="popup_close"></a>');
    $(".popup").append(data);
    $(".popup").show("slow");
}

function closePopup() {
    $(".overlay").hide();
    $(".popup").hide("slow");
    $(".popup").empty();
}


function refreshTable(group, url) {
    loaderOn();
    var groupid;
    if(group == "clients")  groupid = 5;
    else groupid = 4;

    $.ajax({
        type: "GET",
        url: url,
        data: {
            action: "refreshTable",
            group: groupid
        },
        success: function(responce) {
            $(".page").empty();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            loaderOff();
        }
    });
}


function saveUserData(data) {
    loaderOn();
    $.ajax({
        type: "GET",
        url: "../../admin?menuTab=Main&menuAction=updateAction&ajax=1",
        data: data,
        success: function(responce) {
            refreshTable("clients");
            closePopup();
            loaderOff();
        }
    });
}


function changeUserActive(userid, status) {
    $.ajax({
        type: "GET",
        url: "../../admin?menuTab=Main&menuAction=updateActive&ajax=1",
        data: {
            model_name: "User",
            model_id: userid,
            value: status
        },
        success: function(responce){
            var url;
            if(status == "false") url = "client";
            else url = "archive";

            refreshTable("clients", url);
        }
    });
}

function getClientPopup(userid) {
    $.ajax({
        type: "GET",
        url: "client",
        data: {
            action: "updateFormClient",
            userid: userid,
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}

function getTeacherPopup(userid) {
    $.ajax({
        type: "GET",
        url: "teacher",
        data: {
            action: "updateFormTeacher",
            userid: userid,
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}