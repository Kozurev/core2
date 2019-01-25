//var root = "/musadm";
var root = $("#rootdir").val();

$(function(){
    $("body")
        //Открытие всплывающего окна редактирования пользователя
        .on("click", ".user_edit", function(e){
            e.preventDefault();

            var userid = $(this).data("userid");
            var usergroupid = $(this).data("usergroup");


            switch(usergroupid)
            {
                case 2: getManagerPopup(userid);    break;
                case 4: getTeacherPopup(userid);    break;
                case 5: getClientPopup(userid);     break;
                case 6: getDirectorPopup(userid);   break;
            }
        })
        .on("click", ".user_create", function(e){
            e.preventDefault();
            var userid = 0;
            var usergroupid = $(this).data("usergroup");

            switch(usergroupid)
            {
                case 2: getManagerPopup(userid);    break;
                case 4: getTeacherPopup(userid);    break;
                case 5: getClientPopup(userid);     break;
                case 6: getDirectorPopup(userid);   break;
            }
        })
        //Сохранение данных
        .on("click", ".popop_user_submit", function(e){
            e.preventDefault();
            loaderOn();
            userSave(refreshUserTable);
        })
        //Добавление пользователя в архив
        .on("click", ".user_archive", function(){
            var agree = confirm("Перенести пользователя в архив?");
            if(agree != true) return;
            var userid = $(this).data("userid");
            loaderOn();
            updateActive("User", userid, "false", refreshUserTable);
        })
        //"Разархивирование пользователя"
        .on("click", ".user_unarchive", function(){
            var agree = confirm("Убрать пользователя из архива?");
            if(agree != true) return;
            loaderOn();
            var userid = $(this).data("userid");
            updateActive("User", userid, "true", refreshUserTable);
        })
        //Удаление пользователя
        .on("click", ".user_delete", function(e){
            e.preventDefault();
            var userid = $(this).data("model_id");
            deleteItem("User", userid, refreshArchiveTable);
        })
        //Нажатие на кнопку закрытия высплывающего окна редактирования пользователя
        .on("click", ".popup_close", function(e){
            e.preventDefault();
            closePopup();
        })
        //Начисление платежа пользователю (форма)
        .on("click", ".user_add_payment", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            getPaymentPopup(userid, root + "/user/client");
        })
        //Сохранение заметок клиента
        .on("blur", "#client_notes", function(){
            loaderOn();
            var note = $(this).val();
            var userid = $(this).data("userid");
            updateUserNote(userid, note, loaderOff);
        })
        .on("click", "#per_lesson", function(){
            loaderOn();
            var value = 0;
            if($(this).is(":checked"))  value = 1;
            var userid = $(this).data("userid");
            updateUserPerLesson(userid, value, loaderOff);
        })
        //Сохранение логина клиента в личном кабинете
        .on("click", ".change_login_submit", function(e){
            e.preventDefault();
            loaderOn();
            //userSave(loaderOff);
            saveData("User", function(response){ loaderOff(); });

            $("input[name=pass1]").val('');
            $("input[name=pass2]").val('');
        })
        .on("click", ".balance_show", function(e){
            e.preventDefault();
            loaderOn();
            var date_from = $("input[name=date_from]").val();
            var date_to = $("input[name=date_to]").val();
            $.ajax({
                type: "GET",
                url: "",
                data: {
                    date_from: date_from,
                    date_to: date_to
                },
                success: function(responce){
                    $("body").empty();
                    $("body").html(responce);
                    loaderOff();
                }
            });
        })
        .on("click", "#user_comment_save", function(e){
            e.preventDefault();
            var text = $("#user_comment").val();
            if( text != '' )
            {
                loaderOn();
                var userid = $(this).data("userid");
                saveUserComment(userid, text, refreshUserTable);
            }
        })
        .on("click", "#get_lid_data", function(e){
            e.preventDefault();
            var lid_id = $("#lid_id").val();
            if( lid_id == "" || lid_id == "0" )
            {
                $("#lid_id").addClass("error");
                return false;
            }
            loaderOn();

            $.ajax({
                type: "GET",
                url: "",
                dataType: 'json',
                data: {
                    action: "getLidData",
                    lidid: lid_id
                },
                success: function(responce) {
                    if( responce != "0" )
                    {
                        $("input[name=name]").val( responce.name );
                        $("input[name=surname]").val( responce.surname );
                        $("input[name=phoneNumber]").val( responce.phone );
                        $("input[name='property_9[]']").val( responce.vk );
                        $(".get_lid_data_row").remove();
                    }
                    else
                    {
                        alert( "Лида с номером " + lid_id + " не существует" );
                    }
                    loaderOff();
                }
            });

            loaderOff();
        })
        //Поиск клиента на странице менеджера
        .on("submit", "#search_client", function(e){
            e.preventDefault();
            loaderOn();
            var surname = $("#surname").val();
            var name    = $("#name").val();
            var phone   = $("#phone").val();
            if(surname == "" && name == "" && phone == "")
            {
                loaderOff();
                return false;
            }
            searchClients( surname, name, phone );
        })
        .on("click", "#user_search_clear", function(e){
            e.preventDefault();
            $(".dynamic-fixed-row").find(".buttons-panel").remove();
            $(".dynamic-fixed-row").find(".table-responsive").remove();
            $("#surname").val("");
            $("#name").val("");
            $("#phone").val("");
        })
        .on("click", ".info-by-id", function(e){
            e.preventDefault();
            var model = $(this).data("model");
            var id = $(this).data("id");
            getObjectPopupInfo( id, model );
        })
        .on("click", ".events_show", function(e){
            e.preventDefault();
            var from = $("input[name='event_date_from']").val();
            var to = $("input[name='event_date_to']").val();
            loaderOn();
            $.ajax({type:"GET", url:"", data:{action:"refreshTableUsers",event_date_from:from,event_date_to:to}, success:function(response){
                $(".page").html(response);
                loaderOff();
                }});
        })
        .on("click", ".events_load_more", function(){
            loaderOn();
            var limit = $(this).data("limit");
            $.ajax({url:"", type:"GET", data:{action:"refreshTableUsers", limit: limit}, success:function(responce){
                $(".page").html(responce);
                loaderOff();
                }});
        })
        .on("click", ".edit_teacher_report", function(e){
            e.preventDefault();
            var report_id = $(this).data("reportid");

            $.ajax({
                url: root + "/balance",
                type: "GET",
                data: {
                    action: "edit_report_popup",
                    id: report_id
                },
                success: function(response){
                    showPopup(response);
                }
            });
        })
        .on("click", ".report_data_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", function(response){ refreshUserTable(); });
        });
});


function getObjectPopupInfo( id, model ) {
    loaderOn();
    $.ajax({
        url: root,
        type: "GET",
        data: {
            action: "getObjectInfoPopup",
            id: id,
            model: model
        },
        success: function( responce ) {
            showPopup( responce );
            loaderOff();
        }
    });
}


/**
 * Поиск списка клиентов по фамилии, имени и номеру телефона
 */
function searchClients( surname, name, phone ) {
    $.ajax({
        url: root,
        type: "GET",
        data: {
            action: "search_client",
            surname: surname,
            name: name,
            phone: phone
        },
        success: function(response) {
            if( response == "" )    alert( "Пользователи с указаными параметрами не найдены" );
            $(".users").remove();
            $(".dynamic-fixed-row").find(".buttons-panel").remove();
            $(".dynamic-fixed-row").find(".table-responsive").remove();
            $(".dynamic-fixed-row").append( response );
            loaderOff();
        }
    });
}


function userSave(func) {
    var login = $("input[name=login]").val();
    var userid = $("input[name=id]").val();

    $.ajax({
        type: "GET",
        url: root + "/user/client",
        data: {
            action: "checkLoginExists",
            login: login,
            userid: userid
        },
        success: function(responce){
            if(responce != "")
            {
                alert(responce);
                loaderOff();
            }
            else
            {
                if( $("#createData").valid() )
                    saveData("User", function(func){
                        func();
                    });
                else
                    loaderOff();
            }
        }
    });
}


function updateUserNote(userid, note, func) {
    $.ajax({
        type: "GET",
        url: root + "/user/balance",
        data: {
            action: "updateNote",
            userid: userid,
            note: note
        },
        success: function(responce){
            func();
            if(responce != "")  alert(responce);
        }
    });
}


function updateUserPerLesson(userid, value, func) {
    $.ajax({
        type: "GET",
        url: root + "/user/balance",
        data: {
            action: "updatePerLesson",
            userid: userid,
            value: value
        },
        success: function(responce) {
            func();
            if(responce != "")  alert(responce);
        }
    });
}


function getPaymentPopup(userid, url) {
    $.ajax({
        type: "GET",
        url: url,
        data: {
            action: "getPaymentPopup",
            userid: userid
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}


function refreshUserTable() {

    if( $("#search_client").length != 0 )
    {
        $("#search_client").submit();
        return;
    }

    if( $(".users").length == 0 )
    {
        loaderOff();
        return false;
    }

    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "refreshTableUsers",
        },
        success: function(responce) {
            $(".users").empty();
            $(".users").append(responce);
            $("#sortingTable").tablesorter();
            loaderOff();
        }
    });
}


function refreshArchiveTable(func) {
    $.ajax({
        type: "GET",
        url: root + "/user/archive",
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


function getClientPopup(userid) {
    $.ajax({
        type: "GET",
        url: root + "/user/client",
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
        url: root + "/user/teacher",
        data: {
            action: "updateFormTeacher",
            userid: userid,
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function getDirectorPopup(userid) {
    $.ajax({
        type: "GET",
        url: root + "/user/client",
        data: {
            action: "updateFormDirector",
            userid: userid
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}


function getManagerPopup(userid) {
    $.ajax({
        type: "GET",
        url: root + "/user/client",
        data: {
            action: "updateFormManager",
            userid: userid
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}


function saveUserComment( userid, text, func) {
    $.ajax({
        type: "GET",
        url: root + "/balance",
        data: {
            action: "saveUserComment",
            userid: userid,
            text: text
        },
        success: function(responce){
            //$(".page").empty();
            $(".users").html(responce);
            loaderOff();
        }
    });
}