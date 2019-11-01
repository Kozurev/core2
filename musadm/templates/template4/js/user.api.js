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
        return root + '/api/user/index.php';
    };


    /**
     * Формирование списка пользователей с учетом заданых параметров
     * список параметров более детально описан в файле: api/user
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
        userData.append('action', 'save');

        $.ajax({
            type: 'POST',
            url: User.getApiLink(),
            dataType: 'json',
            data: userData,
            contentType: false,
            processData: false,
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
     * @param formSelector
     * @param callBack
     */
    static saveFrom(formSelector, callBack) {
        loaderOn();
        let jqForm = $(formSelector);
        if (jqForm.valid() == false) {
            loaderOff();
        } else {
            let user = new FormData(jqForm.get(0));
            User.save(user, callBack);
        }
    }


    /**
     * Добавление комментария к пользователю
     *
     * @param userId
     * @param comment
     * @param callback
     */
    static saveComment(userId, comment, callback) {
        var ajaxData = comment;
        ajaxData.userId = userId;
        ajaxData.action = 'saveComment';

        $.ajax({
            type: 'POST',
            url: User.getApiLink(),
            dataType: 'json',
            data: ajaxData,
            success: function (response) {
                if (callback != undefined) {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При сохранении комментария пользователя произошла ошибка');
                loaderOff();
            }
        });
    }

}