'use strict'

class User {
    static TYPE_INDIV = 1;
    static TYPE_GROUP = 2;
    static OPERATION_PLUS = 'plus';
    static OPERATION_MINUS = 'minus';
    static OPERATION_SET = 'set';

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/user/api.php';
    };


    /**
     * Формирование списка пользователей с учетом заданых параметров
     * список параметров более детально описан в файле: api/user/api.php
     *
     * @param params
     * @param callBack
     */
    static getList(params, callBack) {
        $.ajax({
            type: 'GET',
            url: User.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getList',
                params: params
            },
            success: function (response) {
                callBack(response);
            }
        });
    }


    /**
     * Так как метод getList не может формировать список клиентов по id препода, а только по значению допю свойства
     * введен дополнительный (промежуточный) метод
     */
    static getListByTeacherId(teacherId, callBack) {
        $.ajax({
            type: 'GET',
            url: User.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getListByTeacherId',
                teacherId: teacherId
            },
            success: function (response) {
                callBack(response);
            }
        });
    }


    /**
     * Сохранение пользователя
     *
     * @param userData
     * @param callBack
     */
    static save(userData, callBack) {
        userData += '&action=save';

        $.ajax({
            type: 'POST',
            url: User.getApiLink(),
            dataType: 'json',
            data: userData,
            success: function (response) {
                callBack(response);
            },
            error: function() {
                notificationError('При сохранении данных пользователя произошла ошибка');
                loaderOff();
            }
        });
    }


    /**
     * Изменение кол-ва занятий у клиента
     *
     * @param userId
     * @param operation
     * @param lessonsType
     * @param num
     * @param callBack
     */
    static changeCountLessons(userId, operation, lessonsType, num, callBack) {
        $.ajax({
            type: 'GET',
            url: User.getApiLink(),
            dataType: 'json',
            data: {
                action: 'changeCountLessons',
                userId: userId,
                operation: operation,
                lessonsType: lessonsType,
                number: num
            },
            success: function(response) {
                callBack(response);
            },
            error: function(){
                notificationError('При изменении кол-ва занятий клиента произошла ошибкаы');
            }
        });
    }



    /**
     * Метод сохранения пользователя из данных формы
     *
     * @param form
     * @param callBack
     */
    static saveFrom(form, callBack) {
        loaderOn();
        if (form.valid() == false) {
            loaderOff();
        } else {
            var user = form.serialize();
            var unchecked = form.find('input[type=checkbox]:unchecked');
            for (var i = 0; i < unchecked.length; i++) {
                user += '&' + $(unchecked[i]).attr('name') + '=0';
            }
            User.save(user, callBack);
        }
    }


    /**
     * Колбек при сохранении данных пользователя
     *
     * @param response
     */
    // static saveClientCallback(response) {
    //     if (typeof response.error !== 'undefined') {
    //         notificationError(response.error.message);
    //     } else {
    //         console.log(response);
    //         var tr = $('#user_' + response.user.id);
    //         console.log(tr);
    //         if (tr.length == 0) {
    //             $('.table').prepend(User.makeClientTr(response));
    //         } else {
    //             //ФИО
    //             tr.find('.user__fio').find('a').text(response.user.surname + ' ' + response.user.name);
    //             //Дата рождения
    //             tr.find('.user__birth').text(' ' + response.additional.prop_28.values[0].value + ' г.р.');
    //             //Соглашение подписано
    //             if (response.additional.prop_18.values[0].value == 1) {
    //                 tr.find('.add__18').html('<span class="contract" title="Соглашение подписано"></span>');
    //             } else {
    //                 tr.find('.add__18').empty();
    //             }
    //             //Телефоны
    //             tr.find('.user__phone').text(response.user.phone);
    //             tr.find('.add__16').text(response.additional.prop_16.values[0].value);
    //             //Баланс
    //             tr.find('.add__12').text(response.additional.prop_12.values[0].value);
    //             //Кол-во занятий
    //             tr.find('.add__13').text(response.additional.prop_13.values[0].value);
    //             tr.find('.add__14').text(response.additional.prop_14.values[0].value);
    //             //Длительность занятия
    //             tr.find('.add__17').text(response.additional.prop_17.values[0].value);
    //             //Филиал
    //             if (response.areas.length > 0) {
    //                 tr.find('.user__areas').text(response.areas[0].title);
    //             } else {
    //                 tr.find('.user__areas').empty();
    //             }
    //
    //         }
    //
    //         closePopup();
    //         loaderOff();
    //     }
    // }


    /**
     * Создание новой строчки в таблице пользователей
     *
     * @param data
     */
    // static makeClientTr(data) {
    //     var user = data.user;
    //     var add = data.additional;
    //
    //     var tr = $('<tr class="neutral" id="user_'+user.id+'" role="row"></tr>');
    //     var
    //         td1 = $('<td></td>'),
    //         td2 = $('<td></td>'),
    //         td3 = $('<td></td>'),
    //         td4 = $('<td></td>'),
    //         td5 = $('<td></td>'),
    //         td6 = $('<td></td>'),
    //         td7 = $('<td width="140px"></td>');
    //
    //     //ФИО
    //     td1.append('<span class="user__fio"><a href="'+root+'/balance/?userid='+user.id+'">'+user.surname+' '+user.name+'</a></span>');
    //     //Соглашение подписано
    //     td1.append('<span class="add__18"></span>');
    //     if (add.prop_18.values[0].value == '1') {
    //         td1.find('.add__18').html('<span class="contract" title="Соглашение подписано"><input type="hidden"/></span>');
    //     }
    //     //Год рождения
    //     td1.append('<span class="user__birth"></span>');
    //     if (add.prop_28.values[0].value != '') {
    //         td1.find('.user__birth').text(add.prop_28.values[0].value+' г.р.');
    //     }
    //     //Поурочная оплата
    //     td1.append('<span class="add__32"></span>');
    //     if (add.prop_32.values[0].value == '1') {
    //         td1.find('.add__32').append('<div class="notes">«Сменный график»</div>');
    //     }
    //     //Примечание
    //     if (add.prop_19.values[0].value != '') {
    //         td1.append('<span class="add__19"><div class="notes">'+add.prop_19.values[0].value+'</div></span>');
    //     }
    //
    //     //Номера телефонов
    //     td2.append('<span class="user__phone">'+user.phone+'</span>');
    //     td2.append('<br/><span class="add__16">'+add.prop_16.values[0].value+'</span>');
    //
    //     //Баланс
    //     td3.append('<span class="add__12">'+add.prop_12.values[0].value+'</span>');
    //
    //     //Занятия
    //     td4.append('<span class="add__13">'+add.prop_13.values[0].value+'</span>');
    //     td4.append(' / <span class="add__13">'+add.prop_14.values[0].value+'</span>');
    //
    //     //Длительность занятия
    //     td5.append('<span class="add__17">'+add.prop_17.values[0].value+'</span>');
    //
    //     //Филиал
    //     td6.append('<span class="user__areas"></span>');
    //     if (data.areas.length > 0) {
    //         td6.find('.user__areas').text(data.areas[0].title);
    //     }
    //
    //     //Действия
    //     if (data.access.payment_create_client) {
    //         td7.append('<a class="action add_payment user_add_payment" href="#" data-userid="'+user.id+'" title="Добавить платеж"></a>');
    //     }
    //     if (data.access.user_edit_client) {
    //         td7.append('<a class="action edit" href="#" onclick="getClientPopup('+user.id+')" title="Редактировать данные"></a>');
    //     }
    //     if (data.access.user_archive_client) {
    //         td7.append('<a class="action archive user_archive" href="#" data-userid="'+user.id+'" title="Переместить в архив"></a>');
    //     }
    //
    //     tr.append(td1);
    //     tr.append(td2);
    //     tr.append(td3);
    //     tr.append(td4);
    //     tr.append(td5);
    //     tr.append(td6);
    //     tr.append(td7);
    //     return tr;
    // }
}