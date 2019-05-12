'use strict';
var root = $('#rootdir').val();

$(function(){
    $("body")
        //Открытие всплывающего окна редактирования пользователя
        .on('click', '.user_edit', function(e) {
            e.preventDefault();
            var userId = $(this).data('userid');
            var usergroupid = $(this).data('usergroup');

            switch(usergroupid)
            {
                case 2: getManagerPopup(userId);    break;
                case 4: getTeacherPopup(userId);    break;
                case 5: getClientPopup(userId);     break;
                case 6: getDirectorPopup(userId);   break;
                default:    break;
            }
        })
        .on('click', '.user_create', function(e) {
            e.preventDefault();
            var userId = 0;
            var usergroupid = $(this).data('usergroup');

            switch(usergroupid)
            {
                case 2: getManagerPopup(userId);    break;
                case 4: getTeacherPopup(userId);    break;
                case 5: getClientPopup(userId);     break;
                case 6: getDirectorPopup(userId);   break;
                default:    break;
            }
        })
        //Сохранение данных
        .on('click', '.popop_user_submit', function(e) {
            e.preventDefault();
            loaderOn();
            userSave(function(response){
                refreshUserTable();
            });
        })
        //Добавление пользователя в архив
        .on('click', '.user_archive', function(e) {
            e.preventDefault();
            var agree = confirm('Перенести пользователя в архив?');
            if (agree != true) return;
            loaderOn();
            var userId = $(this).data('userid');
            var userTr = $(this).parent().parent();
            updateActive('User', userId, 'false', function(response) {
                var
                    totalCountSpan =    $('#total-clients-count'),
                    totalCount =        Number(totalCountSpan.text());

                userTr.remove();
                totalCountSpan.text(totalCount - 1);
                loaderOff();
            });
        })
        //"Разархивирование пользователя"
        .on('click', '.user_unarchive', function(e) {
            e.preventDefault();
            var agree = confirm('Убрать пользователя из архива?');
            if (agree != true) return;
            loaderOn();
            var userId = $(this).data('userid');
            var userTr = $(this).parent().parent();
            updateActive('User', userId, 'true', function(response) {
                var
                    totalCountSpan =    $('#total-clients-count'),
                    totalCount =        Number(totalCountSpan.text());

                totalCountSpan.text(totalCount - 1);
                userTr.remove();
            });
        })
        //Удаление пользователя
        .on('click', '.user_delete', function(e) {
            e.preventDefault();
            var userId = $(this).data('model_id');
            deleteItem('User', userId, refreshArchiveTable);
        })
        //Нажатие на кнопку закрытия высплывающего окна редактирования пользователя
        .on("click", ".popup_close", function(e) {
            e.preventDefault();
            closePopup();
        })
        //Начисление платежа пользователю (форма)
        .on('click', '.user_add_payment', function(e) {
            e.preventDefault();
            var userId = $(this).data('userid');
            getPaymentPopup(userId, root + '/user/client');
        })
        //Сохранение заметок клиента
        .on('blur', '#client_notes', function() {
            loaderOn();
            var note = $(this).val();
            var userId = $(this).data('userid');
            updateUserNote(userId, note, loaderOff);
        })
        .on('click', '#per_lesson', function() {
            loaderOn();
            var value = 0;
            if ($(this).is(':checked')) {
                value = 1;
            }
            var userId = $(this).data('userid');
            updateUserPerLesson(userId, value, loaderOff);
        })
        //Сохранение логина клиента в личном кабинете
        .on("click", ".change_login_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData('User', function(response){ loaderOff(); });
            $('input[name=pass1]').val('');
            $('input[name=pass2]').val('');
        })
        .on('click', '.balance_show', function(e) {
            e.preventDefault();
            loaderOn();
            var dateFrom = $('input[name=date_from]').val();
            var dateTo = $('input[name=date_to]').val();
            $.ajax({
                type: 'GET',
                url: '',
                data: {
                    date_from: dateFrom,
                    date_to: dateTo
                },
                success: function(response) {
                    $('body').html(response);
                    loaderOff();
                }
            });
        })
        .on('click', '#user_comment_save', function(e) {
            e.preventDefault();
            var text = $('#user_comment').val();
            if (text != '') {
                loaderOn();
                var userId = $(this).data('userid');
                saveUserComment(userId, text, refreshUserTable);
            }
        })
        .on('click', '#get_lid_data', function(e) {
            e.preventDefault();
            var lidId = $("#lid_id").val();
            if (lidId == '' || lidId == '0') {
                $('#lid_id').addClass('error');
                return false;
            }
            loaderOn();

            $.ajax({
                type: 'GET',
                url: root + '/user/client',
                dataType: 'json',
                data: {
                    action: 'getLidData',
                    lidId: lidId
                },
                success: function(response) {
                    if (response != '') {
                        $('input[name="name"]').val(response.name);
                        $('input[name="surname"]').val(response.surname);
                        $('input[name="phoneNumber"]').val(response.phone);
                        $('input[name="property_9[]"]').val(response.vk);
                        $('.get_lid_data_row').remove();
                    } else {
                        notificationError("Лида с номером " + lidId + " не существует");
                    }
                    loaderOff();
                },
                error: function (response) {
                    notificationError("Лида с номером " + lidId + " не существует");
                    loaderOff();
                }
            });
        })
        //Поиск клиента на странице менеджера
        .on('submit', '#search_client', function(e) {
            e.preventDefault();
            loaderOn();
            var
                surname = $('#surname').val(),
                name    = $('#name').val(),
                phone   = $('#phone').val();

            if(surname == '' && name == '' && phone == '') {
                loaderOff();
                return false;
            }

            searchClients(surname, name, phone);
        })
        .on('click', '#user_search_clear', function(e) {
            e.preventDefault();
            $('.dynamic-fixed-row').find('.buttons-panel').remove();
            $('.dynamic-fixed-row').find('.table-responsive').remove();
            $('#surname').val('');
            $('#name').val('');
            $('#phone').val('');
        })
        .on('click', '.info-by-id', function(e) {
            e.preventDefault();
            var model = $(this).data('model');
            var id = $(this).data('id');
            getObjectPopupInfo(id, model);
        })
        .on('click', '.events_show', function(e) {
            e.preventDefault();
            var from = $('input[name="event_date_from"]').val();
            var to = $('input[name="event_date_to"]').val();
            loaderOn();
            $.ajax({type:'GET', url:'', data:{action:'refreshTableUsers',event_date_from:from,event_date_to:to}, success:function(response){
                $('.page').html(response);
                loaderOff();
            }});
        })
        .on('click', '.events_load_more', function() {
            loaderOn();
            var limit = $(this).data('limit');
            $.ajax({url:'', type:'GET', data:{action:'refreshTableUsers', limit: limit}, success:function(response){
                $('.page').html(response);
                loaderOff();
            }});
        })
        .on('click', '.edit_teacher_report', function(e) {
            e.preventDefault();
            var reportId = $(this).data('reportid');

            $.ajax({
                url: root + '/balance',
                type: 'GET',
                data: {
                    action: 'edit_report_popup',
                    id: reportId
                },
                success: function(response) {
                    showPopup(response);
                }
            });
        })
        .on('click', '.report_data_submit', function(e) {
            e.preventDefault();
            loaderOn();
            saveData('Main', function(response){ refreshUserTable(); });
        })
        .on('click', '#show-client-filter', function() {
            var
                form = $('#client-filter'),
                i    = $(this).find('i');

            if (form.css('display') == 'none') {
                i.css('transform', 'rotate(180deg)');
                form.show('fast');
            } else {
                i.css('transform', 'none');
                form.hide('fast');
            }
        })
        .on('submit', '#client-filter', function(e) {
            e.preventDefault();
            applyClientFilter($('#client-filter'), function(response) {
                $('.users').html(response);
            });
        });
});


/**
 * Применение фильтров для поиска клиентов
 *
 * @param form
 * @param callback
 */
function applyClientFilter(form, callback) {
    loaderOn();
    var
        action =    form.attr('action'),
        data =      form.serialize();
    data += '&action=applyUserFilter';
    $.ajax({
        type: 'GET',
        url: action,
        data: data,
        success: function(response) {
            if(typeof callback === 'function') {
                callback(response);
            }
            loaderOff();
        }
    });
}


/**
 * Информация об объекте: пользователь, задача или лид
 *
 * @param id
 * @param model
 */
function getObjectPopupInfo(id, model) {
    loaderOn();
    $.ajax({
        url: root,
        type: 'GET',
        data: {
            action: 'getObjectInfoPopup',
            id: id,
            model: model
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        }
    });
}


/**
 * Поиск списка клиентов по фамилии, имени и номеру телефона
 *
 * @param surname
 * @param name
 * @param phone
 */
function searchClients(surname, name, phone) {
    $.ajax({
        url: root,
        type: 'GET',
        data: {
            action: 'search_client',
            surname: surname,
            name: name,
            phone: phone
        },
        success: function(response) {
            if (response == '') {
                notificationError('Пользователи с указаными параметрами не найдены');
            }
            var dynamicRow = $('.dynamic-fixed-row');
            $('.users').remove();
            dynamicRow.find('.buttons-panel').remove();
            dynamicRow.find('.table-responsive').remove();
            dynamicRow.append(response);
            loaderOff();
        }
    });
}


/**
 * Сохранение данных учетной записи клиента
 *
 * @param callback
 */
function userSave(callback) {
    var login = $('input[name=login]').val();
    var userId = $('input[name=id]').val();

    $.ajax({
        type: 'GET',
        url: root + '/user/client',
        data: {
            action: 'checkLoginExists',
            login: login,
            userId: userId
        },
        success: function(response) {
            if (response != '') {
                alert(response);
                loaderOff();
            } else {
                if ($('#createData').valid())
                    saveData('User', function(response) {
                        callback(response);
                    });
                else {
                    loaderOff();
                }
            }
        }
    });
}


/**
 * Сохранение значения доп. свойства пользователя - примечание
 *
 * @param userId
 * @param note
 * @param callback
 */
function updateUserNote(userId, note, callback) {
    $.ajax({
        type: 'GET',
        url: root + '/user/balance',
        data: {
            action: 'updateNote',
            userId: userId,
            note: note
        },
        success: function(response) {
            callback();
            if (response != '') {
                notificationError(response);
            }
        }
    });
}


/**
 * Изменение чекбокса клиента со значением свойства "поурочно"
 *
 * @param userId
 * @param value
 * @param callback
 */
function updateUserPerLesson(userId, value, callback) {
    $.ajax({
        type: 'GET',
        url: root + '/user/balance',
        data: {
            action: 'updatePerLesson',
            userId: userId,
            value: value
        },
        success: function(response) {
            callback();
            if (response != '') {
                notificationError(response);
            }
        }
    });
}

/**
 * Открытие всплывающего окна для создания платежа
 *
 * @param userId
 * @param url
 */
function getPaymentPopup(userId, url) {
    $.ajax({
        type: 'GET',
        url: url,
        data: {
            action: 'getPaymentPopup',
            userId: userId
        },
        success: function(response) {
            showPopup(response);
        },
        error: function (response) {
            notificationError(response);
        }
    });
}

/**
 * Обновление контента страницы пользователей
 *
 * @returns {boolean}
 */
function refreshUserTable() {
    //Если это страница менеджера то просто происходит отправка данных формы поиска клиента
    //а там уже свой обработчик обновляет контент
    if ($('#search_client').length != 0) {
        $('#search_client').submit();
        return;
    }

    if ($('.users').length == 0) {
        loaderOff();
        return false;
    }

    $.ajax({
        type: 'GET',
        url: '',
        async: false,
        data: {
            action: 'refreshTableUsers'
        },
        success: function(response) {
            $('.users').html(response);
            $('#sortingTable').tablesorter();
            loaderOff();
        }
    });
}


/**
 * Обновление контента страницы с архивом пользователей
 *
 * @param callback
 */
function refreshArchiveTable(callback) {
    $.ajax({
        type: 'GET',
        url: root + '/user/archive',
        data: {
            action: 'refreshTableArchive'
        },
        success: function(response) {
            $('.page').html(response);
            $('#sortingTable').tablesorter();
            if (typeof callback == 'function') {
                callback(response);
            }
        }
    });
}


/**
 * Открытие всплывающего окна редактирования данных клиента
 *
 * @param userId
 */
function getClientPopup(userId) {
    $.ajax({
        type: 'GET',
        url: root + '/user/client',
        data: {
            action: 'updateFormClient',
            userId: userId
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


/**
 * Открытие всплывающего окна редактирования данных преподавателя
 *
 * @param userId
 */
function getTeacherPopup(userId) {
    $.ajax({
        type: 'GET',
        url: root + '/user/teacher',
        data: {
            action: 'updateFormTeacher',
            userId: userId
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


/**
 * Открытие всплывающего окна редактирования данных директора
 *
 * @param userId
 */
function getDirectorPopup(userId) {
    $.ajax({
        type: 'GET',
        url: root + '/user/client',
        data: {
            action: 'updateFormDirector',
            userId: userId
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


/**
 * Открытие всплывающего окна редактирования данных директора
 *
 * @param userId
 */
function getManagerPopup(userId) {
    $.ajax({
        type: 'GET',
        url: root + '/user/client',
        data: {
            action: 'updateFormManager',
            userId: userId
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


/**
 * Создание комментария к клиенту
 *
 * @param userId
 * @param text
 * @param callback
 */
function saveUserComment(userId, text, callback) {
    $.ajax({
        type: 'GET',
        url: root + '/balance',
        data: {
            action: 'saveUserComment',
            userId: userId,
            text: text
        },
        success: function(response) {
            $('.users').html(response);
            if (typeof callback == 'function') {
                callback(response);
            }
            loaderOff();
        }
    });
}


function usersExport(href, form) {
    var link = href + '?action=export';

    if (form === undefined) {
        location = link;
    } else if (form.serialize() != '') {
        location = link + '&' + form.serialize();
    }
}