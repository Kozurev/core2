var loaderTime = 0;



function getCurrentDate() {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    if(month < 10)  month = "0" + month;
    if(day < 10)    day = "0" + day;

    var today = year + "-" + month + "-" + day;
    return today;
}

//Запуск лоадера
function loaderOn(){
    var bodyHeight = ($(window).height() - $(".loader").outerHeight()) / 2;
    if(bodyHeight < window.innerHeight) bodyHeight = window.innerHeight;
    $(".loader").css("height", bodyHeight);
    $(".loader").show();
}

//Отключение лоадера
function loaderOff(){
    $(".loader").hide();
    $("#sortingTable").tablesorter();
}


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


function saveData(link, func) {
    var form = $("#createData");
    if(form.valid() == false)
    {
        loaderOff();
        return;
    }
    var data = form.serialize();
    var aUnchecked = form.find("input[type=checkbox]:unchecked");
    for (var i = 0; i < aUnchecked.length; i++) {
        data += "&" + $(aUnchecked[i]).attr("name") + "=0";
    }

    //alert(link + "?menuTab=Main&menuAction=updateAction&ajax=1");
    $.ajax({
        type: "GET",
        url: link,
        data: data,
        success: function(responce) {
            closePopup();
            if(responce != "0" && responce != "") alert(responce);
            if($.isFunction(func)) func();
        }
    });
}


/**
 *	Удаление объекта
 */
function deleteItem(model_name, model_id, link, func){

    var url = link;
    url += "&model_name=" + model_name;
    url += "&model_id=" + model_id;

    var agree = confirm("Подтвердите действие");
    if(agree != true) return;

    $.ajax({
        type: "GET",
        url: url,
        success: function(answer){
            func();
            loaderOff();
            if(answer != "0")
                alert("Ошибка: " + answer);
        }
    });
}


function savePayment(userid, value, description, type, url, func) {
    $.ajax({
        type: "GET",
        url: url,
        async: false,
        data: {
            action: "savePayment",
            userid: userid,
            value: value,
            type: type,
            description: description
        },
        success: function(responce){
            if(responce != "0") alert("Ошибка: " + responce);
            closePopup();
            func();
        }
    });
}