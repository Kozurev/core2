"use strict";
var root = $('#rootdir').val();

$(function(){
    let days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

    //Отмена перехода по ссылке при клике на элемент выпадающего списка
    $('.submenu').on('click', 'a', function(e){ e.preventDefault(); });

    $('body')
        //Подгрузка данных расписания при изменении даты в календаре
        .on('change', '.schedule_calendar', function() {
            loaderOn();
            let
                date = $('.schedule_calendar').val(),
                userid = $('#userid').val(),
                newDate = new Date($('.schedule_calendar').val()),
                dayName = days[newDate.getDay()];
            $('.day_name').text(dayName);
            getSchedule(userid, date, loaderOff);
        })

        //Открытие всплывающего окна создания периода отсутствия
         .on('click', '.schedule_absent', function(e) {
            e.preventDefault();
            let
                userId =  $(this).parent().parent().data('clientid'),
                typeId =    $(this).parent().parent().data('typeid'),
                date =      $('#schedule_calendar').val();
            getScheduleAbsentPopup(userId, typeId, date);
        })

         //Занесение преподавателя в стоп-лист
        .on('click', 'input[name=teacher_stop_list]', function() {
            var userId = $(this).data('user_id');
            var value = $(this).prop('checked');
            savePropertyValue('teacher_stop_list', value, 'User', userId, loaderOff);
        })

        //Сохранение данных периода отсутствия
        .on('click', '.popop_schedule_absent_submit', function(e) {
            e.preventDefault();
            loaderOn();

            let
                form = $('#createData'),
                absentData = {};

            absentData.id = form.find('input[name=id]').val();
            absentData.objectId = form.find('input[name=objectId]').val();
            absentData.dateFrom = form.find('input[name=dateFrom]').val();
            absentData.dateTo = form.find('input[name=dateTo]').val();
            absentData.timeFrom = form.find('input[name=timeFrom]').val();
            absentData.timeTo = form.find('input[name=timeTo]').val();
            absentData.typeId = form.find('input[name=typeId]').val();

            if ($('#absent_add_task').is(':checked')) {
                addAbsentTask(absentData.dateTo, absentData.objectId);
            }

            Schedule.saveAbsentPeriod(absentData, function (response) {
                if (checkResponseStatus(response)) {
                    var msg = 'Период отсутствия с ' + response.absent.refactoredDateFrom + ' ';
                    if (response.absent.refactoredTimeFrom != '00:00') {
                        msg += response.absent.refactoredTimeFrom;
                    }
                    msg += ' по ' + response.absent.refactoredDateTo + ' ';
                    if (response.absent.refactoredTimeTo != '00:00') {
                        msg += response.absent.refactoredTimeTo;
                    }
                    msg += ' успешно сохранен';

                    notificationSuccess(msg);
                    closePopup();
                    if ($('.users').length == 0) {
                        refreshSchedule();
                    } else {
                        refreshUserTable();
                    }
                } else {
                    closePopup();
                    loaderOff();
                }
            });
        })

        //Открытие всплывающего окна создания занятия
        .on('click', '.add_lesson', function() {
            var date = $(this).data('date');
            var lessonDate = new Date(date);
            var currentDate = new Date(getCurrentDate());

            if (lessonDate.valueOf() < currentDate.valueOf()) {
                return false;
            }

            var type = $(this).data('schedule_type');
            var classId = $(this).data('class_id');
            var areaId = $(this).data('area_id');
            getScheduleLessonPopup(classId, date, areaId, type);
        })

        //Сохранение данных занятия
        .on("click", ".popop_schedule_lesson_submit", function(e) {
            e.preventDefault();
            loaderOn();

            var Form = $('#createData');
            var clientId = Form.find('select[name=clientId]').val();
            var teacherId = Form.find('select[name=teacherId]').val();
            var date = Form.find('input[name=insertDate]').val();
            var timeFrom = Form.find('input[name=timeFrom]').val();
            var timeTo = Form.find('input[name=timeTo]').val();
            var areaId = Form.find('input[name=areaId]').val();
            var lessonType = Form.find('input[name=lessonType]').val();
            var typeId = Form.find('select[name=typeId]').val();
            var isCreateTask = $('input[name=is_create_task]');

            //Проверка преподавателя на отсутствие
            Schedule.checkAbsentPeriod({
                userId: teacherId,
                date: date,
                timeFrom: timeFrom,
                timeTo: timeTo
            }, function (response) {
                if (response.isset == true) {
                    alert('В указанное время преподаватель отсутствует');
                    loaderOff();
                } else {


                    //Если это индивидуальное занятие
                    if (typeId == 1) {
                        Schedule.checkAbsentPeriod({userId: clientId, date: date}, function (response) {
                            //Если есть существующий период отсутсвия
                            if (response.isset == true) {
                                //Постановка в основной график
                                if (lessonType == 1) {
                                    if (confirm('В данное время у клиента существует активный период отсутсвия с '
                                        + response.period.dateFrom[1] + ' по ' + response.period.dateTo[1] + '. Хотите продолжить?')) {
                                        saveData('Main', function (response) {
                                            if (response == false)
                                            {
                                                addTask(isCreateTask,clientId,date,areaId);
                                            }
                                            refreshSchedule();
                                        });
                                    } else {
                                        loaderOff();
                                    }
                                }
                                //Постановка в актуальный график
                                else {
                                    alert('Постановка клиента в расписание на данную дату невозможна, так как у него имеется активный'
                                        + ' период отсутствия с ' + response.period.dateFrom[1] + ' по ' + response.period.dateTo[1]);
                                    loaderOff();
                                }
                            }
                            else {
                                saveData('Main', function (response) {
                                    if (response == false)
                                    {
                                        addTask(isCreateTask,clientId,date,areaId);
                                    }
                                    refreshSchedule();
                                });
                            }

                        });
                    } else {
                        if(typeId ==3){
                            checkPropertyValue('teacher_stop_list','User',teacherId,
                                function(data){
                                if(data == true){
                                    alert('Преподаватель в стоп листе, постановка консультации невозможна!!!');
                                    loaderOff();
                                } else {
                                    saveData('Main', function (response) {
                                        refreshSchedule();
                                    });

                                }
                            });
                        }
                        else {
                            //Сделал сразу заготовку для добавления задачи группе
                            saveData('Main', function (response) {
                                if (response == false && (typeId == 1 || typeId == 2))
                                {
                                    addTask(isCreateTask,clientId,date,areaId);
                                }
                                refreshSchedule();
                            });
                        }
                    }
                }
            });

        })

        //Удаление занятия из основного графика
        .on('click', '.schedule_delete_main', function(e) {
            e.preventDefault();
            loaderOn();
            var lessonid = $(this).data('id');
            var deletedate = $(this).data('date');
            markDeleted(lessonid, deletedate, refreshSchedule);
        })

        //Выставка отметки об разовом отсутствии занятия
        .on('click', '.schedule_today_absent', function(e) {
            e.preventDefault();
            loaderOn();
            var lessonid = $(this).parent().parent().data('id');
            var date = $(this).parent().parent().data('date');
            markAbsent(lessonid, date, refreshSchedule);
            loaderOff();
        })

        //Открытие всплывающего окна для редактирования времени проведения занятия
        .on('click', '.schedule_update_time', function(e) {
            e.preventDefault();
            var lessonid = $(this).parent().parent().data('id');
            var date = $(this).parent().parent().data('date');
            getScheduleChangeTimePopup(lessonid, date);
        })

        //Сохранение изменения времени проведения занятия
        .on('click', '.popop_schedule_time_submit', function(e) {
            e.preventDefault();
            loaderOn();
            var timeFrom = $('input[name=timeFrom]').val();
            var timeTo = $('input[name=timeTo]').val();
            var lessonId = $('input[name=lesson_id]').val();
            var date = $('input[name=date]').val();
            saveScheduleChangeTimePopup(lessonId, date, timeFrom, timeTo, refreshSchedule);
        })

        /**
         * Подгрузка элементов выпадающего списка в зависимости от типа занятия:
         * индивидуальное, групповое или консультация
         */
        .on('change', 'select[name=typeId]', function() {
            loaderOn();
            var type = $(this).val();
            var select = $('.clients');

            if (type != 0) {
                select.show();
            } else {
                select.hide();
            }

            var rememberRow = $('#createData').find('.remember');

            if (type == 3) {
                var select = $('select[name=clientId]');
                var selectBlock = select.parent();
                select.remove();
                selectBlock.append("<input type='number' name='clientId' class='form-control' placeholder='Номер лида' />");
                $.each(rememberRow, function(index, value){
                    $(value).hide();
                });
                loaderOff();
            } else {
                var input = $('input[name=clientId]');
                var clientsList = $('#createData').find('select[name=clientId]');
                if (input.length > 0) {
                    var inputBlock = input.parent();
                    inputBlock.append("<select name='clientId' class='form-control valid' ></select>");
                    clientsList = inputBlock.find('select');
                    input.remove();
                }

                $.each(rememberRow, function(index, value){
                    $(value).show();
                });

                clientsList.empty();
                if (type == 1) {
                    User.getList({
                        select: ['id', 'surname', 'name'],
                        active: true,
                        groups: [5],
                        order: { surname: 'ASC' }
                    }, function(users) {
                        clientsList.append('<option value="0"> ... </option>');
                        $.each(users, function(key, user) {
                            clientsList.append('<option value="'+user.id+'">'+user.surname + ' ' + user.name +'</option>');
                        });
                        loaderOff();
                    });
                } else {
                    Group.getList({active: true}, function (groups) {
                        $.each(groups, function (key, group) {
                            clientsList.append('<option value="'+group.id+'">'+group.title+'</option>');
                        });
                        loaderOff();
                    });
                }
            }
        })

        /**
         * Формирование списка клиентов по принадлежности к преподавателю
         */
        .on('change', 'select[name=teacherId]', function(e){
            var lessonTyeId = $('select[name=typeId]').val();
            if (lessonTyeId == 1) {
                var
                    clientsList = $('select[name=clientId]'),
                    selectedClient = clientsList.val(),
                    selectedTeacher = $('select[name=teacherId]').val();

                if (selectedClient > 0) {
                    return false;
                }

                loaderOn();

                if (selectedTeacher == 0) {
                    clientsList.empty();
                    User.getList({
                        select: ['id', 'surname', 'name'],
                        active: true,
                        groups: [5],
                        order: {surname: 'ASC'}
                    }, function (users) {
                        clientsList.append('<option value="0"> ... </option>');
                        $.each(users, function (key, user) {
                            clientsList.append('<option value="' + user.id + '">' + user.surname + ' ' + user.name + '</option>');
                        });
                        loaderOff();
                    });
                } else {
                    clientsList.empty();
                    User.getListByTeacherId(selectedTeacher, function (users) {
                        clientsList.append('<option value="0"> ... </option>');
                        $.each(users, function (key, user) {
                            clientsList.append('<option value="' + user.id + '">' + user.surname + ' ' + user.name + '</option>');
                        });
                        loaderOff();
                    });
                }
            }
        })

        /**
         * Отправка отчета преподавателем о проведении занятия
         */
        .on('click', '.send_report', function(e) {
            e.preventDefault();
            loaderOn();
            var tr = $(this).parent().parent();
            var lessonId = tr.find('input[name=lessonId]').val();
            var date = tr.find('input[name=date]').val();
            var typeId = tr.find('input[name=typeId]').val();
            var attendance = tr.find('input[type=checkbox]');
            var note = tr.find('input[name=note]');
            var fileInput = tr.find('input[type=file]');

            var ajaxData = {
                action: 'teacherReport',
                date: date,
                lessonId: lessonId
            };

            if (typeId == 2) {
                $.each(attendance, function(key, input) {
                    var name = $(input).attr('name');
                    if (name != 'group') {
                        ajaxData[name] = Number($(input).is(':checked'));
                    } else {
                        ajaxData['attendance'] = Number($(input).is(':checked'));
                    }
                });
            } else {
                ajaxData['attendance'] = Number($(attendance[0]).is(':checked'));
            }

            //Отправка данных о проведенном занятии
            $.ajax({
                type: 'GET',
                url: '',
                data: ajaxData,
                dataType: 'json',
                success: function(response) {
                    if (checkResponseStatus(response)) {
                        //Создание комментария лиду
                        if (typeId == 3 && note.lendth != 0 && note.val() != '') {
                            Lids.saveComment(0, note.data('lidid'), note.val(), function(response){
                                if (checkResponseStatus(response)) {
                                    FileManager.upload(0, 1, fileInput, 'Comment', response.id, function(file) {
                                        refreshSchedule();
                                    });
                                }
                            });
                        } else {
                            refreshSchedule();
                        }
                    }
                },
                error: function(response) {
                    notificationError('Произошла ошибка: ' + response);
                    loaderOff();
                }
            });
        })

        //Удаление отчета о проведении занятия
        .on('click', '.delete_report', function(e) {
            e.preventDefault();
            loaderOn();
            var tr = $(this).parent().parent();
            var lessonId = tr.find('input[name=lessonId]').val();
            var date = tr.find('input[name=date]').val();

            $.ajax({
                type: 'GET',
                url: '',
                data: {
                    action: 'deleteReport',
                    lesson_id: lessonId,
                    date: date
                },
                success: function(response) {
                    if (response != '') {
                        alert(response);
                    }
                    refreshSchedule();
                }
            });
        })
        /**
         * Сохранение данных задачи из раздела расписания
         */
        .on('click', '.popop_schedule_task_submit', function(e) {
            e.preventDefault();
            loaderOn();
            var form = $('#createData');

            if (form.valid()) {
                var formData = form.serialize();
                saveScheduleTask(formData, loaderOff);
            } else {
                loaderOff();
            }
        })

        //Сохранение выплаты преподавателю
        .on('click', '.add_teacher_payment', function(e) {
            e.preventDefault();
            loaderOn();
            var date = $('.teacher_payments').find('input[name=date]').val();
            var summ = $('.teacher_payments').find('input[name=summ]').val();
            var user = $('.teacher_payments').find('input[name=userid]').val();
            var description = $('.teacher_payments').find('input[name=description]').val();
            saveTeacherPayment(user, summ, date, description, refreshSchedule);
        })

        //Указание месяца / года клиентом и подгрузка расписания за выбранный период
        .on('change', '.client_schedule', function(){
            loaderOn();

            var month = $('#month').val();
            var year = $('#year').val();
            var userid = $('#userid').val();

            $.ajax({
                type: 'GET',
                url: '',
                data: {
                    ajax: 1,
                    year: year,
                    month: month,
                    userid: userid
                },
                success: function(response) {
                    $('.users').html(response);
                    loaderOff();
                }
            });
        })

        //Открытие всплывающего окна редактирования филиала
        .on('click', '.schedule_area_edit', function(e) {
            e.preventDefault();
            var areaId = $(this).data('area_id');
            getScheduleAreaPopup(areaId);
        })

        //Сохранение данных филиала
        .on('click', '.popop_schedule_area_submit', function(e) {
            e.preventDefault();
            loaderOn();
            saveData('Main', function(response){refreshAreasTable();});
        })

        //Изменение активности филлиала
        .on('click', 'input[name=schedule_area_active]', function() {
            var areaId = $(this).data('area_id');
            var value = $(this).prop('checked');
            updateActive('Schedule_Area', areaId, value, loaderOff);
        })

        //Удаление филиала
        .on('click', '.schedule_area_delete', function(e) {
            e.preventDefault();
            loaderOn();
            var areaId = $(this).data('area_id');
            deleteItem('Schedule_Area', areaId, refreshAreasTable);
        });


    /**
     * Формирование текущей даты для календаря
     */
    var today = new Date();
    var day =   today.getDate();
    var month = today.getMonth() + 1;
    var year =  today.getFullYear();

    $(".day_name").text( days[today.getDay()] );

    if (day < 10)    day = '0' + day;
    if (month < 10)  month = '0' + month;

    var result = year + '-' + month + '-' + day;
    $('.schedule_calendar').val(result);
});


// Создание задачи с напоминанием
function addTask(isCreateTask,clientId,date,areaId) {
    if (isCreateTask.is(':checked')) {
        $.ajax({
            type: 'GET',
            url: '',
            data: {
                action: 'create_schedule_task',
                date: date,
                clientId: clientId,
                areaId: areaId
            }
        });
    }
    isCreateTask.remove();
}

function refreshAreasTable() {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'getSchedule',
        },
        success: function(responSe) {
            $('.page').html(responSe);
            loaderOff();
        }
    });
}


function saveTeacherPayment(user, summ, date, description, func) {
    $.ajax({
        type: 'GET',
        url: root + '/admin?menuTab=Main&menuAction=updateAction&ajax=1',
        async: false,
        data: {
            id: '',
            modelName: 'Payment',
            user: user,
            value: summ,
            type: 3,
            datetime: date,
            description: description
        },
        success: function(responSe) {
            if (responSe != '0') alert('Ошибка: ' + responce);
            closePopup();
            func();
        }
    });
}


function getScheduleAreaPopup(areaId) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'getScheduleAreaPopup',
            areaId: areaId
        },
        success: function(response){
            showPopup(response);
        }
    });
}


function saveScheduleTask(formData, func) {
    formData += '&action=save_task';

    $.ajax({
        type: 'GET',
        url: '',
        data: formData,
        success: function(response) {
            if(response != '0') {
                alert(response);
            } else {
                notificationSuccess('Ваше обращение доставлено менеджерам');
            }
            closePopup();
            func();
            loaderOff();
        }
    });
}


function refreshSchedule() {
    $('.schedule_calendar').trigger('change');
    $('#month').trigger('change');
}

function getSchedule(userId, date, func) {
    $.ajax({
        type: 'GET',
        url: '',
        async: false,
        data: {
            action: 'getSchedule',
            userid: userId,
            date: date,
        },
        success: function(response) {
            $('.schedule').html(response);
            func();
            loaderOff();
        }
    });
}


/**
 * Открытие всплывающего окна создания/редактирования периода отсутствия
 *
 * @param objectId
 * @param typeId
 * @param date
 * @param id
 */
function getScheduleAbsentPopup(objectId, typeId, date, id) {
    $.ajax({
        type: 'GET',
        url: root + '/schedule',
        async: false,
        data: {
            action: 'getScheduleAbsentPopup',
            objectId: objectId,
            typeId: typeId,
            date: date,
            id: id
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


/**
 * Удаление периода отсутствия клиента
 *
 * @param id
 * @param callback
 */
function deleteScheduleAbsent(id, callback) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/schedule',
        dataType: 'json',
        data: {
            action: 'deleteScheduleAbsent',
            id: id
        },
        success: function(response) {
            if (typeof callback == 'function') {
                callback(response);
            }
        },
        error: function() {
            notificationError('При удалении периода отсутсвия произошла ошибка');
            loaderOff();
        }
    });
}


/**
 * Колбэк для удаления периода отсутствия клиента
 *
 * @param response
 */
function deleteAbsentClientCallback(response) {
    notificationSuccess('Период отсутствия ' + response.fio + ' с ' + response.dateFrom + ' по '
        + response.dateTo + ' успешно удален');
    $('.row[data-period-id='+response.id+']').remove();
    let absentRow = $('#absent-row');
    if (absentRow.find('.periods').find('div').length == 0) {
        absentRow.remove();
    }
    loaderOff();
}


function getScheduleLessonPopup(classId, date, areaId, lessonType) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'getScheduleLessonPopup',
            classId: classId,
            date: date,
            lessonType: lessonType,
            areaId: areaId
        },
        success: function(response) {
            showPopup(response);
            var clientsSelect = $('select[name=typeId]');
            clientsSelect.val('1');
            clientsSelect.trigger('change');
        }
    });
}

function markDeleted(lessonId, deleteDate, func) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'markDeleted',
            lessonid: lessonId,
            deletedate: deleteDate
        },
        success: function(response) {
            func();
        }
    });
}

function markAbsent(lessonId, date, func) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'markAbsent',
            lessonid: lessonId,
            date: date
        },
        success: function(response) {
            func();
        }
    });
}


function getScheduleChangeTimePopup(lessonId, date) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'getScheduleChangeTimePopup',
            id: lessonId,
            date: date
        },
        success: function(response) {
            showPopup(response)
        }
    });
}


function saveScheduleChangeTimePopup(lessonId, date, timeFrom, timeTo, func) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'saveScheduleChangeTimePopup',
            lesson_id: lessonId,
            date: date,
            time_from: timeFrom,
            time_to: timeTo
        },
        success: function(response){
            if (response != '') {
                alert(response);
            }
            closePopup();
            func();
        }
    });
}


function addAbsentTask(dateTo, clientId) {
    $.ajax({
        type: 'GET',
        url: root + '/schedule',
        data: {
            action: 'addAbsentTask',
            date_to: dateTo,
            client_id: clientId
        },
        success: function(response) {
            if (response != '') {
                alert(responce);
            }
        }
    });
}


/**
 *
 * @param areaId
 * @param classId
 * @param td
 * @returns void
 */
function scheduleEditClassName(areaId, classId, td) {
    if ($(td).find('.prevName').length > 0) {
        return;
    }

    if (!confirm('Вы хотите переименовать класс?')) {
        return;
    }

    var prevName = $(td).text();
    $(td).empty();

    var hiddenSpan = '<span class="prevName" style="display: none">'+prevName+'</span>';
    var editField = '<input class="form-control" id="newClassName" autofocus value="'+prevName+'" ' +
        'onblur="scheduleOnblurClassName('+areaId+', '+classId+', this.value)" />';

    $(td).append(hiddenSpan);
    $(td).append(editField);
}


function scheduleSaveClassName(areaId, classId, newName, callBack) {
    $.ajax({
        type: 'GET',
        url: root + '/schedule',
        dataType: 'json',
        data: {
            action: 'saveClassName',
            areaId: areaId,
            classId: classId,
            newName: newName
        },
        success: function (response) {
            if (typeof callBack === 'function') {
                callBack(response);
            }
        }
    });
}


function scheduleOnblurClassName(areaId, classId, newValue) {
    var editField = $('#newClassName');
    var td = editField.parent();
    var prevName = td.find('.prevName');

    if (!confirm('Сохранить изменения?')) {
        td.text(prevName.text());
        editField.remove();
        prevName.remove();
    } else {
        loaderOn();
        scheduleSaveClassName(areaId, classId, newValue, function(response) {
            td.empty();
            td.text(newValue);
            loaderOff();
        });
    }
}