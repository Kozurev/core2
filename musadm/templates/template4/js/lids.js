"use strict";
var root = $('#rootdir').val();

$(function () {
    $(document)
        .on('click', '.lid_submit', function (e) {
            e.preventDefault();
            loaderOn();
            var data = '';
            data += 'surname=' + $('input[name=surname]').val();
            data += '&comment=' + $('textarea[name=comment]').val();
            data += '&name=' + $('input[name=name]').val();
            data += '&number=' + $('input[name=number]').val();
            data += '&vk=' + $('input[name=vk]').val();
            data += '&status_id=' + $('select[name=status_id]').val();
            data += '&area_id=' + $('select[name=area_id]').val();
            data += '&control_date=' + $('input[name=control_date]').val();
            data += '&source_select=' + $('select[name=source_select]').val();
            data += '&source_input=' + $('input[name=source_input]').val();
            closePopup();
            saveLid(data, refreshLidTable);
        })
        .on('click', '.add_lid_comment', function (e) {
            e.preventDefault();
            var lidid = $(this).data('lidid');
            getCommentPopup(lidid);
        })
        .on('click', '.popop_lid_comment_submit', function (e) {
            e.preventDefault();
            loaderOn();
            if ($('#createData').find('textarea').val() == '') {
                closePopup();
                loaderOff();
                return;
            }
            saveData('Main', function (response) {
                refreshLidTable();
            });
        })
        .on('change', '.lid_status', function () {
            loaderOn();
            var lidid = $(this).data('lidid');
            var statusid = $(this).val();
            changeStatus(lidid, statusid, refreshLidTable);
        })
        .on('change', '.lid_date', function () {
            loaderOn();
            var lidid = $(this).data('lidid');
            var date = $(this).val();
            changeDate(lidid, date, loaderOff);
        })
        .on('click', '.lids_show', function () {
            loaderOn();
            refreshLidTable();
        })
        .on('click', '.search', function(e) {
            e.preventDefault();
            loaderOn();
            var lidId = $('#search_id').val();
            if (lidId == '') {
                $('#search_id').addClass('error');
                alert('Введите номер лида в соответствующее поле');
                loaderOff();
                return false;
            }
            findLid(lidId);
        })
        .on('change', '.lid-area', function () {
            var areaId = $(this).val();
            var lidId = $(this).data('lid-id');
            updateLidArea(lidId, areaId, function (response) {});
        })
        .on('click', '.create_lid', function (e) {
            e.preventDefault();
            editLidPopup('');
        })
        .on('click', '.show_lid_status', function(e) {
            e.preventDefault();
            var statusesTable = $('.lid_statuses_table');

            if(statusesTable.css('display') == 'block') {
                statusesTable.hide('slow');
            } else {
                statusesTable.show('slow');
            }
        })
        .on('click', '.edit_lid_status', function(e) {
            e.preventDefault();
            var statusId = $(this).data('id');
            getLidStatusPopup(statusId);
        })
        .on('click', '.lid_status_submit', function(e) {
            e.preventDefault();

            var
                Form =      $('#createData'),
                id =        Form.find('input[name=id]').val(),
                title =     Form.find('input[name=title]').val(),
                itemClass = Form.find('select[name=item_class]').val();

            saveLidStatus(id, title, itemClass, function(response) {
                closePopup();

                var statusSelects = $('.lid_status');

                if(id == '') {
                    var newTr = '<tr>' +
                        '<td>' + response.title + '</td>' +
                        '<td>' + response.colorName + '</td>' +
                        '<td>' +
                        '   <input type="radio" name="lid_status_consult" id="lid_status_consult_'+response.id+'" value="'+response.id+'">' +
                        '   <label for="lid_status_consult_'+response.id+'"></label></td>' +
                        '<td>' +
                        '   <input type="radio" name="lid_status_consult_attended" id="lid_status_consult_attended_'+response.id+'" value="'+response.id+'">' +
                        '   <label for="lid_status_consult_attended_'+response.id+'"></label>' +
                        '</td>' +
                        '<td>' +
                        '   <input type="radio" name="lid_status_consult_absent" id="lid_status_consult_absent_'+response.id+'" value="'+response.id+'">' +
                        '   <label for="lid_status_consult_absent_'+response.id+'"></label>' +
                        '</td>' +
                        '<td class="right">' +
                            '<a class="action edit edit_lid_status" data-id="' + response.id + '"></a>' +
                            '<a class="action delete delete_lid_status" data-id="' + response.id + '"></a>' +
                        '</td>' +
                        '</tr>';

                    var
                        table = $('#table-lid-statuses'),
                        lastTr = table.find('tr')[table.find('tr').length - 1],
                        lastTrClone = $(lastTr).clone();

                    $(lastTr).remove();
                    table.append(newTr);
                    table.append(lastTrClone);

                    $.each(statusSelects, function(key, select) {
                        $(select).append('<option value="' + response.id + '">' + response.title + '</option>');
                    });
                } else {
                    var
                        tr = $('.lid_statuses_table').find('.edit[data-id='+id+']').parent().parent(),
                        tdTitle = tr.find('td')[0],
                        tdColor = tr.find('td')[1];

                    $(tdTitle).text(response.title);
                    $(tdColor).text(response.colorName);

                    $.each(statusSelects, function(key, select) {
                        $(select).find('option[value='+response.id+']').text(response.title);
                    });

                    if(response.oldItemClass) {
                        var editingStatusCards = $('.' + response.oldItemClass);
                        $.each(editingStatusCards, function(key, card) {
                            $(card).removeClass(response.oldItemClass);
                            $(card).addClass(response.itemClass);
                        });
                    }
                }
            });
        })
        .on('click', '.delete_lid_status', function(e) {
            e.preventDefault();

            var id = $(this).data('id');

            deleteLidStatus(id, function(response) {
                $('.lid_statuses_table').find('.edit[data-id='+response.id+']').parent().parent().remove();

                var statusSelects = $('.lid_status');
                $.each(statusSelects, function(key, select) {
                    $(select).find('option[value='+response.id+']').remove();
                });

                var deletedStatusItems = $('.' + response.itemClass);
                $.each(deletedStatusItems, function(key, card) {
                    $(card).removeClass(response.itemClass);
                });
            });
        })
        .on('change', '#source_select', function(e) {
            var sourceInput = $('#source_input');

            if($(this).val() == 0) {
                sourceInput.show();
            } else {
                sourceInput.val('');
                sourceInput.hide();
            }
        });


        $('#table-lid-statuses').on('change', 'input[type=radio]', function() {
            var
                propName =      $(this).attr('name'),
                propVal =       $(this).val(),
                directorId =    $('#directorid').val(),
                statusName =    $(this).parent().parent().find('td')[0];
            statusName = $(statusName).text();

            savePropertyValue(propName, propVal, 'User', directorId, function() {
                var msg = 'Статусом лида после ';

                switch(propName)
                {
                    case 'lid_status_consult':
                        msg += 'создания консультации';
                        break;
                    case 'lid_status_consult_attended':
                        msg += 'посещения консультации';
                        break;
                    case 'lid_status_consult_absent':
                        msg += 'пропуска консультации';
                        break;
                    default: msg = 'Неизвестной настройке';
                }

                msg += ' установлен: \''+ statusName +'\'';
                notificationSuccess(msg);
            });
        });
});


/**
 * Перезагрузка блока с классом lids
 */
function refreshLidTable() {
    var dateFrom = $("input[name=date_from]").val();
    var dateTo = $("input[name=date_to]").val();

    $.ajax({
        type: 'GET',
        url: '',
        async: false,
        data: {
            action: 'refreshLidTable',
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function (response) {
            $('.lids').html(response);
            loaderOff();
        }
    });
}


/**
 * Изменение филиала лида
 *
 * @param lidId
 * @param areaId
 * @param func
 */
function updateLidArea(lidId, areaId, func) {
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'updateLidArea',
            lid_id: lidId,
            area_id: areaId
        },
        success: function (response) {
            if(response != '') {
                notificationError('Ошибка: ' + response);
            } else {
                notificationSuccess('Изменения успешно сохранены');
            }

            func(response);
        },
        error: function (response) {
            notificationError('При изменении филлиала лида произошла ошибка');
        }
    });
}


/**
 * Открытие всплывающего окна создания лида
 *
 * @param lidId
 */
function editLidPopup(lidId) {
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'editLidPopup',
            lid_id: lidId
        },
        success: function (response) {
            showPopup(response);
        }
    });
}


/**
 * Поиск лида по id
 *
 * @param id - id лида
 */
function findLid(id) {
    $.ajax({
        type: 'GET',
        url: '',
        async: false,
        data: {
            action: 'refreshLidTable',
            lidid: id
        },
        success: function (responce) {
            $('.lids').html(responce);
            loaderOff();
        }
    });
}


/**
 * Открытие всплывающего окна для добавления комментария
 *
 * @param lidid - id лида
 */
function getCommentPopup(lidid) {
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'add_note_popup',
            model_id: lidid
        },
        success: function (responce) {
            showPopup(responce);
        }
    });
}


/**
 * Сохранение лида
 *
 * @param data - данные формы создания лида
 * @param func - функция, выполняющаяся после сохранения
 */
function saveLid(data, func) {
    $.ajax({
        type: 'GET',
        url: root + '/lids?action=save_lid',
        async: false,
        data: data,
        success: function(response) {
            if(response != '') {
                notificationError('При сохранении лида произошла ошибка: ' + response);
            } else {
                notificationSuccess('Лид успешно сохранен');
            }
            func();
        },
        error: function (response) {
            notificationError('Ошибка');
        }
    });
}


/**
 * Обработчик изменения статуса лида
 *
 * @param lidid - id лида
 * @param statusid - id статуса
 * @param func - функция выполняющаяся после выполнения
 */
function changeStatus(lidid, statusid, func) {
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'changeStatus',
            model_id: lidid,
            status_id: statusid
        },
        success: function (response) {
            if(response != '') {
                notificationError('Ошибка: ' + response);
            } else {
                notificationSuccess('Статус лида успешно изменен');
            }

            func();
        }
    });
}


/**
 * Изменение даты контроля лида
 *
 * @param lidid
 * @param date
 * @param func
 */
function changeDate(lidid, date, func) {
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        async: false,
        data: {
            action: 'changeDate',
            model_id: lidid,
            date: date
        },
        success: function (response) {
            if(response != '') {
                notificationError('Ошибка: ' + response);
            } else {
                notificationSuccess('Дата контроля лида успешно изменена на: <br/>' + date);
            }

            func();
        }
    });
}


/**
 * Открытие всплывающего окна создания/редактирования статуса лида
 *
 * @param id
 */
function getLidStatusPopup(id) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'getLidStatusPopup',
            id: id
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        },
        error: function(response) {
            closePopup();
            notificationError('Ошибка: редактируемый статус не существует либо принадлежит другой организации');
            loaderOff();
        }
    });
}


/**
 * Создание/редактирование данных статуса лида
 *
 * @param id
 * @param title
 * @param itemClass
 * @param callback
 */
function saveLidStatus(id, title, itemClass, callback) {
    loaderOn();

    $.ajax({
        type: 'GET',
        url: root + '/lids',
        dataType: 'json',
        data: {
            action: 'saveLidStatus',
            id: id,
            title: title,
            item_class: itemClass
        },
        success: function(response) {
            callback(response);
            loaderOff();
        },
        error: function(response) {
            notificationError('При сохранении статуса лида произошла ошибка');
        }
    });
}


/**
 * Удаление статуса лида
 *
 * @param id
 * @param callback
 */
function deleteLidStatus(id, callback) {
    loaderOn();

    $.ajax({
        type: 'GET',
        url: root + '/lids',
        dataType: 'json',
        data: {
            action: 'deleteLidStatus',
            id: id
        },
        success: function(response) {
            callback(response);
            loaderOff();
        },
        error: function(response) {
            closePopup();
            notificationError('Ошибка: удаляемый статус не существует либо принадлежит другой организации');
            loaderOff();
        }
    });
}