var loaderTime = 0;
var root = "/musadm";


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


/**
 * Вызов всплывающего окна
 *
 * @param data - html данные окна
 */
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


/**
 * Сохранение данных объекта
 *
 * @param tab - вкладка админ меню, на которую будет отправлен запрос
 * @param func - выполняемая функция по получению ответа ajax-запроса
 */
function saveData(tab, func) {
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

    var link = root + "/admin?menuTab=" + tab + "&menuAction=updateAction&ajax=1";

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
 *
 * @param model_name - название класса объекта
 * @param model_id - id объекта
 * @param func - выполняемая функция после ответа ajax-запроса
 */
function deleteItem(model_name, model_id, func){

    var url = root + "/admin?menuTab=Main&menuAction=deleteAction&ajax=1";
    url += "&model_name=" + model_name;
    url += "&model_id=" + model_id;

    var agree = confirm("Подтвердите действие");
    if(agree != true) return;

    $.ajax({
        type: "GET",
        url: url,
        success: function(answer){
            if(typeof func === "function") func();
            loaderOff();
            if(answer != "0")
                alert("Ошибка: " + answer);
        }
    });
}


/**
 *	Изменение активности структуры или элемента
 *
 *	@param model_name - название объекта (Structure, Structure_Item и т.д.)
 *	@param model_id - id объекта
 *	@param value - значение активности true/false
 *  @param func - выполняемая функция по получению результата ajax-запроса
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
 * Сохранение платежа
 *
 * @param userid - id пользователя, к которому привязан платеж
 * @param value - сумма платежа
 * @param description - описание платежа (примечание)
 * @param type - тип платежа (зачисление, списание и т.д.)
 * @param url - адрес запроса после root + "/user/" (client || balance)
 * @param func - выполняемая функция по получению ответа от ajax-запроса
 */
function savePayment(userid, value, description, description2, type, url, func) {
    $.ajax({
        type: "GET",
        url: root + "/user/" + url,
        async: false,
        data: {
            action: "savePayment",
            userid: userid,
            value: value,
            type: type,
            description: description,
            property_26: description2
        },
        success: function(responce){
            if(responce != "0") alert("Ошибка: " + responce);
            closePopup();
            if(typeof func === "function") func();
        }
    });
}