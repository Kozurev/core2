var loaderTime = 200;
//var root = "/musadm";
var root = $("#rootdir").val();

$(function(){

    if(window.location.hash == "")
    {
        window.location.hash = "#admin?menuTab=Structure&menuAction=show";
    }

    $(document)
    //Обновление рабочей области административного раздела
        .on("click", ".link", function(e){
            e.preventDefault();
            var link = $(this).attr("href");
            window.location.hash = link;
        })
        //Обработка изменения активности элемента
        .on("click", ".activeCheckbox", function(e){
            var model_name = $(this).attr("model_name");
            var model_id = $(this).attr("model_id");
            var value = $(this).prop("checked");
            updateActive(model_name, model_id, value);
        })
        //Обработчик удаления элемента
        .on("click", ".delete", function(e){
            e.preventDefault();
            var model_name = $(this).data("model_name");
            var model_id = $(this).data("model_id");
            deleteItem(model_name, model_id);
        })
        //Сохранение даных
        .on("click", ".submit", function(e){
            e.preventDefault();
            var form = $("#createData");
            if(form.valid() == false)	return;
            var data = form.serialize();
            var aUnchecked = form.find("input[type=checkbox]:unchecked");
            for (var i = 0; i < aUnchecked.length; i++)
            {
                data += "&" + $(aUnchecked[i]).attr("name") + "=0";
            }
            var link = $(this).attr("href");
            updateItem(data, link);
        })
        //Добавление поля для дополнительного свойства
        .on("click", ".add_new_value", function(e){
            e.preventDefault();

            var aBlocks = $(this).parent().find(".field");
            var lastBlock = $(aBlocks)[aBlocks.length - 1];
            var appendedBlock = $(lastBlock).clone();

            if($(aBlocks).length == 1)
            {
                appendedBlock.append('<div class="delete_block"></div>');
            }

            var button = $(this).parent().find(".add_new_value").clone();

            $(this).parent().append(appendedBlock);
            $(this).parent().append(button);

            //Удаление лишней кнопки "Добавить"
            var aButtons = $(this).parent().find(".add_new_value");
            $(aButtons)[0].remove();
        })
        //Удаление поля дополнительного свойства
        .on("click", ".delete_block", function(){
            $(this).parent().remove();
        })
        //Следующая страница
        .on("click", ".next_page", function(e){
            e.preventDefault();
            var pageData = $(this).attr("href");
            var current_page = Number($("#current_page").text());
            var count_pages = Number($("#count_pages").text());
            if(current_page == count_pages)	return;
            //var hash = window.location.hash;
            setPage(current_page);
        })
        //Предыдущая страница
        .on("click", ".prev_page", function(e){
            e.preventDefault();
            var pageData = $(this).attr("href");
            var current_page = Number($("#current_page").text());
            var count_pages = Number($("#count_pages").text());
            if(current_page == 1)	return;
            setPage(current_page-2);
        })
        //Активация/деактивация свойства для объекта
        .on("click", ".active_property_for_object", function(){
            var model_name = $(this).data("obj_name");
            var model_id = $(this).data("obj_id");
            var prop_id = $(this).data("prop_id");
            var active = $(this).is(':checked');
            changePropertyForObject(model_id, model_name, prop_id, active);
        });
});


/**
 * Устанавливает номер страницы (пагинация)
 * @param page
 */
function setPage(page) {
    var hash = window.location.hash;
    if(hash.indexOf("&page") >= 0)	hash = hash.substring(0, hash.indexOf("&page"));
    hash += "&page=" + page;
    window.location.hash = hash;
}

/**
 *	Перезагрузка рабочей области административного раздела
 *	обработка перехода по ссылкам
 *	@param hash - хэш
 */
function reloadMain(hash){
    loaderOn();
    link = hash.substr(6); //форматирование хеша (удаление из строки первого символа '#')
    //alert(link);
    $.ajax({
        type: "GET",
        url: link + "&ajax=1",
        success: function(data){
            $(".main").html(data);
            setTimeout("loaderOff()", loaderTime);
            //loaderOff();
        }
    });
}


/**
 *	Изменение активности структуры или элемента
 *
 *	@param model_name - название объекта (Structure, Structure_Item и т.д.)
 *	@param model_id - id объекта
 *	@param value - значение активности true/false
 */
function updateActive(model_name, model_id, value, func){
    loaderOn();

    var link = root + "/admin?menuTab=Main&menuAction=updateActive&ajax=1";
    link += "&model_name=" + model_name;
    link += "&model_id=" + model_id;
    link += "&value=" + value;
    link += "&ajax=1";

    $.ajax({
        type: "GET",
        url: link,
        success: function(answer){
            if(answer != "0")
                alert("Ошибка: " + answer);

            if(typeof func === "function") func();
            setTimeout("loaderOff()", loaderTime);
        }
    });
}


/**
 *	Удаление объекта
 */
function deleteItem(model_name, model_id){

    var link = "?menuTab=Main&menuAction=deleteAction&ajax=1";
    link += "&model_name=" + model_name;
    link += "&model_id=" + model_id;

    var agree = confirm("Вы действительно хотите удалить объект?");
    if(agree != true) return;

    loaderOn();

    $.ajax({
        type: "GET",
        url: link,
        success: function(answer){
            reloadMain(window.location.hash);
            setTimeout("loaderOff()", loaderTime);
            //loaderOff();
            if(answer != "0")
                alert("Ошибка: " + answer);
        }
    });
}


/**
 * Обновление значений свойств объекта
 * @param objectData
 */
function updateItem(objectData, link){
    loaderOn();

    //var link = "?menuTab=Main&menuAction=updateAction&ajax=1&" + objectData;
    link += "&ajax=1&" + objectData;

    $.ajax({
        type: "GET",
        url: link,
        success: function(answer){
            //reloadMain("#" + link);
            window.history.back();
            setTimeout("loaderOff()", loaderTime);
            //loaderOff();
            if(answer != "0")
                alert("Ошибка: " + answer);
        }
    });
}


function changePropertyForObject(obj_id, obj_name, prop_id, active) {
    loaderOn();

    var link = "?menuTab=Properties&menuAction=changePropertiesList&ajax=1";

    $.ajax({
        type: "GET",
        url: link,
        data: {
            model_id: obj_id,
            model_name: obj_name,
            property_id: prop_id,
            active: active
        },
        success: function(responce) {
            //reloadMain(window.location.hash);
            setTimeout("loaderOff()", loaderTime);
            if(responce != "0")
                alert("Ошибка: " + responce);
        }
    });

}

window.onhashchange = function(){
    reloadMain(window.location.hash);
}

window.onload = function(){
    reloadMain(window.location.hash);
}

// //Запуск лоадера
// function loaderOn(){
//     $(".loader").show();
// }
//
// //Отключение лоадера
// function loaderOff(){
//     $(".loader").hide();
// }


//Запуск лоадера
function loaderOn(){
    var bodyHeight = ($(window).height() - $(".loader").outerHeight()) / 2;
    if(bodyHeight < window.innerHeight) bodyHeight = window.innerHeight;
    $("body").addClass("bodyLoader");
    $(".loader").css("height", bodyHeight);
    $(".loader").show();
}

//Отключение лоадера
function loaderOff(){
    $("body").removeClass("bodyLoader");
    $(".loader").hide();
}