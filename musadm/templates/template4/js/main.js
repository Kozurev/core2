"use strict";

var root = $('#rootdir').val();


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
    var
        overlay = $('.overlay'),
        popup = $('.popup');

    overlay.show();
    popup.empty();
    popup.append('<a href="#" class="popup_close"></a>');
    popup.append(data);
    popup.show('fast');
}

function closePopup() {
    var
        overlay = $('.overlay'),
        popup = $('.popup');

    overlay.hide();
    popup.hide('fast');
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
        async: false,
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
 * Сохранение платежа
 *
 * @param userId - id пользователя, к которому привязан платеж
 * @param value - сумма платежа
 * @param description - описание платежа (примечание)
 * @param adminNote - описание платежа (примечание)
 * @param type - тип платежа (зачисление, списание и т.д.)
 * @param url - адрес запроса после root + "/user/" (client || balance)
 * @param func - выполняемая функция по получению ответа от ajax-запроса
 */
function savePayment(userId, value, description, adminNote, type, url, func) {
    $.ajax({
        type: 'GET',
        url: root + '/' + url,
        data: {
            action: 'savePayment',
            userid: userId,
            value: value,
            type: type,
            description: description,
            property_26: adminNote
        },
        success: function(response) {
            if(response != '0') {
                notificationError('Ошибка: ' + responce);
            }
            closePopup();
            if(typeof func === 'function') {
                func(response);
            }
        },
        error: function(response) {
            notificationError('Произошла ошибка при сохранении платежа');
        }
    });
}