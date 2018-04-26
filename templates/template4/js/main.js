var loaderTime = 0;

$(function() {

    if (window.location.hash == "")
    {
        //window.location.hash = "#user/client";
    }

    $(document)
    //Обновление рабочей области административного раздела
        .on("click", ".link", function (e) {
            e.preventDefault();
            var link = $(this).attr("href");
            window.location.hash = link;
        });

});


/**
 *	Перезагрузка рабочей области административного раздела
 *	обработка перехода по ссылкам
 *	@param hash - хэш
 */
function reloadMain(hash){
    loaderOn();
    link = hash.substr(1); //форматирование хеша (удаление из строки первого символа '#'')

    //alert(link);
    if(link == "")
    {
        setTimeout("loaderOff()", loaderTime);
        return;
    }

    $.ajax({
        type: "GET",
        url: link,// + "&ajax=1",
        success: function(data){
            $(".page").html(data);
            setTimeout("loaderOff()", loaderTime);
        }
    });
}


window.onhashchange = function(){
    reloadMain(window.location.hash);
}

window.onload = function(){
    reloadMain(window.location.hash);
}

//Запуск лоадера
function loaderOn(){
    var bodyHeight = ($(window).height() - $(".loader").outerHeight()) / 2;
    if(bodyHeight < window.innerHeight) bodyHeight = window.innerHeight;
    //$("body").addClass("bodyLoader");
    $(".loader").css("height", bodyHeight);
    $(".loader").show();
}

//Отключение лоадера
function loaderOff(){
    //$("body").removeClass("bodyLoader");
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
            if($.isFunction(func))
                func();
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

    var agree = confirm("Вы действительно хотите удалить объект?");
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
