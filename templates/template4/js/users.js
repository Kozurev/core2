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
        .on("click", ".user_create", function(e){
            e.preventDefault();
            var userid = 0;
            var usergroupid = $(this).data("usergroup");

            if(usergroupid == 5)
                getClientPopup(userid);
            else
                getTeacherPopup(userid);
        })
        //Сохранение данных
        .on("click", ".popop_user_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("../admin?menuTab=User&menuAction=updateAction&ajax=1", refreshUserTable);
            //refreshUserTable();
        })
        //Добавление пользователя в архив
        .on("click", ".user_archive", function(){
            var agree = confirm("Перенести пользователя в архив?");
            if(agree != true) return;
            var userid = $(this).data("userid");
            loaderOn();
            changeUserActive(userid, "false");
        })
        //"Разархивирование пользователя"
        .on("click", ".user_unarchive", function(){
            var agree = confirm("Убрать пользователя из архива?");
            if(agree != true) return;
            loaderOn();
            var userid = $(this).data("userid");
            changeUserActive(userid, "true");
        })
        .on("click", ".user_delete", function(e){
            e.preventDefault();
            var userid = $(this).data("model_id");
            deleteItem("User", userid, "../admin?menuTab=Main&menuAction=deleteAction&ajax=1", refreshArchiveTable);
        })
        //Нажатие на кнопку закрытия высплывающего окна редактирования пользователя
        .on("click", ".popup_close", function(e){
            e.preventDefault();
            closePopup();
        });
});


function refreshUserTable() {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "refreshTableUsers",
            //group: groupid
        },
        success: function(responce) {
            $(".page").empty();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            loaderOff();
        }
    });
}


function refreshArchiveTable(func) {
    $.ajax({
        type: "GET",
        url: "archive",
        data: {
            action: "refreshTableArchive"
        },
        success: function(responce) {
            $(".page").empty();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            if(func) func();
        }
    });
}


function changeUserActive(userid, status) {
    $.ajax({
        type: "GET",
        url: "../admin?menuTab=Main&menuAction=updateActive&ajax=1",
        data: {
            model_name: "User",
            model_id: userid,
            value: status
        },
        success: function(responce){
            var url;
            if(status == "false") url = "client";
            else url = "archive";

            refreshUserTable("clients", url, loaderOff);
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