"use strict";
var root = $('#rootdir').val();

$(function(){
    var days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];

    /**
     * Отмена перехода по ссылке при клике на элемент выпадающего списка
     */
    $('.submenu').on('click', 'a', function(e){ e.preventDefault(); });


    $('body')

        //Подгрузка данных расписания при изменении даты в календаре
        .on('change', '.schedule_calendar', function() {
            loaderOn();
            var date = $('.schedule_calendar').val();
            var userid = $('#userid').val();
            var newDate = new Date($('.schedule_calendar').val());
            var dayName = days[newDate.getDay()];
            $('.day_name').text(dayName);
            getSchedule(userid, date, loaderOff);
        })

        //Открытие всплывающего окна создания периода отсутствия
         .on('click', '.schedule_absent', function(e) {
            e.preventDefault();
            var
                clientid =  $(this).parent().parent().data('clientid'),
                typeid =    $(this).parent().parent().data('typeid'),
                date =      $('#schedule_calendar').val();

            getScheduleAbsentPopup(clientid, typeid, date);
        })

        //Сохранение данных периода отсутствия
        .on('click', '.popop_schedule_absent_submit', function(e) {
            e.preventDefault();
            loaderOn();

            var
                dateTo = $('input[name=dateTo]').val(),
                dateFrom = $('input[name=dateFrom]').val(),
                clientId = $("input[name=clientId]").val();

            if( $('#absent_add_task').is(':checked')) {
                addAbsentTask(dateTo, clientId);
            }

            saveData('Main', function(response) {
                if ($('.users').length == 0) {
                    refreshSchedule();
                } else {
                    refreshUserTable();
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
            var class_id = $(this).data('class_id');
            var area_id = $(this).data('area_id');
            getScheduleLessonPopup(class_id, date, area_id, type);
        })

        //Сохранение данных данятия
        .on("click", ".popop_schedule_lesson_submit", function(e) {
            e.preventDefault();
            loaderOn();

            var Form = $('#createData');
            //Проверка кратности количества минут времени начала и окончания занятия
            var timestep = Form.find('#timestep').val();
            timestep = Number(timestep.substring(3, 5));
            var timeFrom = Form.find('input[name=timeFrom]').val();
            var timeTo = Form.find('input[name=timeTo]').val();
            var timeFromMinutes = Number(timeFrom.substring(3));
            var timeToMinutes = Number(timeTo.substring(3));
            var isTimeError = false;
            var inputSelector;

            if (timeFromMinutes % timestep > 0) {
                isTimeError = true;
                inputSelector = 'From';
            }

            if (timeToMinutes % timestep > 0) {
                isTimeError = true;
                inputSelector = 'To';
            }

            if (isTimeError == true) {
                var input = Form.find('input[name=time' + inputSelector + ']');
                input.addClass('error');
                input.parent().append('<label ' +
                    'id="time'+ inputSelector +'-error" ' +
                    'class="error" ' +
                    'for="time'+ inputSelector +'" ' +
                    '>Количество минут указываемого времени занятия должно быть кратным ' + timestep + '</label>');

                loaderOff();
                return false;
            }

            //Создание задачи с напоминанием
            if ($('input[name=is_create_task]').is(':checked')) {
                var clientId = Form.find('select[name=clientId]').val();
                var date = Form.find('input[name=insertDate]').val();

                $.ajax({
                    type: 'GET',
                    url: '',
                    data: {
                        action: 'create_schedule_task',
                        date: date,
                        client_id: clientId
                    }
                });
            }

            saveData('Main', function(response) { refreshSchedule(); });
        })

        //УДаление занятия из основного графика
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
                var inputBlock = input.parent();
                input.remove();
                inputBlock.append("<select name='clientId' class='form-control valid' ></select>");

                $.each(rememberRow, function(index, value){
                    $(value).show();
                });

                $.ajax({
                    type: 'GET',
                    url: '',
                    data: {action: 'getclientList', type: type},
                    success: function(response) {
                        var select = $('select[name=clientId]');
                        select.empty();
                        select.append(response);
                        loaderOff();
                    }
                });
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
            var teacherId = tr.find('input[name=teacherId]').val();
            var clientId = tr.find('input[name=clientId]').val();
            var typeId = tr.find('input[name=typeId]').val();
            var date = tr.find('input[name=date]').val();
            var lessonType = tr.find('input[name=lessonType]').val();
            var attendance = tr.find('input[name=attendance]').is(':checked');

            if(attendance == true) {
                attendance = 1;
            } else {
                attendance = 0;
            }

            $.ajax({
                type: 'GET',
                url: '',
                data: {
                    action: 'teacherReport',
                    teacher_id: teacherId,
                    client_id: clientId,
                    type_id: typeId,
                    date: date,
                    lesson_id: lessonId,
                    lesson_type: lessonType,
                    attendance: attendance
                },
                success: function(response) {
                    if (response != '0') {
                        alert(responce);
                    }
                    refreshSchedule();
                }
            });
        })

        //Удаление отчета о проведении занятия
        .on('click', '.delete_report', function(e) {
            e.preventDefault();
            loaderOn();
            var tr = $(this).parent().parent();
            var id = tr.find('input[name=reportId]').val();
            var lessonId = tr.find('input[name=lessonId]').val();
            var lessonType = tr.find('input[name=lessonType]').val();

            $.ajax({
                type: 'GET',
                url: '',
                data: {
                    action: 'deleteReport',
                    lesson_id: lessonId,
                    lesson_type: lessonType,
                    report_id: id,
                },
                success: function(response) {
                    if (response != "0") {
                        alert(response);
                    }
                    refreshSchedule();
                }
            });
        })

        //Открытие всплывающего окна о создании задачи из раздела расписания
        // .on("click", ".schedule_task_create", function(){
        //     newScheduleTaskPopup();
        // })

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
            showPopup(responce);
        }
    });
}


function newScheduleTaskPopup() {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'new_task_popup',
        },
        success: function(response) {
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
            if(responce != '0') {
                alert(response);
            }
            closePopup();
            func();
            loaderOff();
        }
    });
}


function refreshSchedule() {
    $(".schedule_calendar").trigger('change');
    $("#month").trigger('change');
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


function getScheduleAbsentPopup(clientId, typeId, date, id) {
    $.ajax({
        type: 'GET',
        url: root + '/schedule',
        async: false,
        data: {
            action: 'getScheduleAbsentPopup',
            client_id: clientId,
            type_id: typeId,
            date: date,
            id: id
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


function getScheduleLessonPopup(classId, date, areaId, type) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'getScheduleLessonPopup',
            class_id: classId,
            date: date,
            model_name: type,
            area_id: areaId
        },
        success: function(response) {
            showPopup(response);
            $('select[name=typeId]').val('1');
            $('select[name=typeId]').trigger('change');
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
            id: lessonid,
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
                alert(responce);
            }
            closePopup();
            func();
        }
    });
}


function addAbsentTask(dateTo, clientId, prevName) {
    $.ajax({
        type: 'GET',
        url: '',
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