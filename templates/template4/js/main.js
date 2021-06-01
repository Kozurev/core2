"use strict";

var root = $('#rootdir').val();

$.ajaxSetup({
    cache: false,
    async: true,
    headers: { "cache-control": "no-cache" },
});

/**
 * Проверка ответа API от сервера на наличие "успеха" или "ошибки"
 *
 * @param response
 * @returns {boolean}
 */
function checkResponseStatus(response) {
    let result;
    if (response.status == undefined) {
        result = true;
    } else if (response.status == true) {
        notificationSuccess(response.message);
        result = true;
    } else if (response.status == false) {
        notificationError(response.message);
        result = false;
    } else {
        result = false;
    }
    return result;
}

/**
 *
 * @param response
 */
function showResponseNotification(response) {
    let status, message;
    status = (response.status === undefined || response.status === true) ? 'success' : 'error';
    message = response.message !== undefined ? response.message : 'Неизвестная ошибка';
    swal({
        type: status,
        title: message
    });
}

function getCurrentDate() {
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    if(month < 10)  month = '0' + month;
    if(day < 10)    day = '0' + day;

    var today = year + '-' + month + '-' + day;
    return today;
}

//Запуск лоадера
function loaderOn() {
    $('.loader').show();
}

//Отключение лоадера
function loaderOff(){
    $('.loader').hide();
    $('#sortingTable').tablesorter();
    $('.sortingTable').tablesorter();
}


/**
 * Вызов всплывающего окна
 *
 * @param data - html данные окна
 */
function showPopup(data) {
    if (data != undefined) {
        prependPopup(data);
    }
    $('.popup').show();
}

function prependPopup(data, popupSize) {
    let
        overlay = $('.overlay'),
        popup = $('.popup');

    overlay.show();
    popup.empty();
    popup.append('<a href="#" class="popup_close"></a>');
    popup.append(data);

    if (popupSize != undefined) {
        popup.css({'width' : popupSize + '%', 'left' : (100 - popupSize) / 2 + '%'});
    }
}

function closePopup() {
    let
        overlay = $('.overlay'),
        popup = $('.popup');

    overlay.hide();
    popup.removeAttr('style');
    popup.hide();
    popup.empty();
}


$('.overlay').click(function(e) {
    closePopup();
});


/**
 * Сохранение данных объекта
 *
 * @param tab - вкладка админ меню, на которую будет отправлен запрос
 * @param func - выполняемая функция по получению ответа ajax-запроса
 */
function saveData(tab, func) {
    var form = $('#createData');
    if(form.valid() == false) {
        loaderOff();
        return false;
    }

    var
        data = form.serialize(),
        unchecked = form.find('input[type=checkbox]:unchecked');

    for (var i = 0; i < unchecked.length; i++) {
        data += '&' + $(unchecked[i]).attr('name') + '=0';
    }

    var link = root + '/admin?menuTab=' + tab + '&menuAction=updateAction&ajax=1';

    $.ajax({
        type: 'GET',
        url: link,
        data: data,
        // async: false,
        success: function(response) {
            closePopup();

            if(response != '0' && response != '') {
                notificationError(response);
            }

            func(response);
        }
    });
}


/**
 *	Удаление объекта
 *
 * @param modelName - название класса объекта
 * @param modelId - id объекта
 * @param func - выполняемая функция после ответа ajax-запроса
 * @return void
 */
function deleteItem(modelName, modelId, func) {
    var agree = confirm('Подтвердите действие');
    if(agree != true) {
        loaderOff();
        return false;
    }

    $.ajax({
        type: 'GET',
        url: root + '/admin',
        data: {
            menuTab: 'Main',
            menuAction: 'deleteAction',
            ajax: 1,
            model_name: modelName,
            model_id: modelId
        },
        success: function(response) {
            if(typeof func === 'function') {
                func(response);
            }

            if(response != '0') {
                notificationError('Ошибка: ' + response);
            }

            loaderOff();
        },
        error: function(response) {
            notificationError('Произошла ошибка при удалении элемента');
        }
    });
}


/**
 *	Изменение активности структуры или элемента
 *
 *	@param modelName - название объекта (Structure, Structure_Item и т.д.)
 *	@param modelId - id объекта
 *	@param value - значение активности true/false
 *  @param func - выполняемая функция по получению результата ajax-запроса
 */
function updateActive(modelName, modelId, value, func) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/admin',
        data: {
            menuTab: 'Main',
            menuAction: 'updateActive',
            ajax: 1,
            model_name: modelName,
            model_id: modelId,
            value: value
        },
        success: function(response) {
            if(response != '0') {
                notificationError('Ошибка: ' + response);
            }

            if(typeof func === 'function') {
                func(response);
            }

            loaderOff();
        },
        error: function(response) {
            notificationError('Произошла ошибка при изменении активности элемента');
            loaderOff();
        }
    });
}


/**
 * Аналог PHP функции empty
 *
 * @param val
 * @returns {boolean}
 */
function empty(val) {
    if (val == undefined || val == 0 || val == '' || val == '0' || val == null) {
        return true;
    } else {
        return false;
    }
}