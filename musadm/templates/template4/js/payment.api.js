

class Payment {

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/payment/api.php';
    };


    /**
     * Метод для получения информации об обном конкретном платеже
     *
     * @param paymentId
     * @param callBack
     */
    static getPayment(paymentId, callBack) {
        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getPayment',
                paymentId: paymentId
            },
            success: function(response) {
                if (callBack != undefined) {
                    callBack(response);
                }
            },
            error: function() {
                notificationError('Произошла ошибка');
            }
        });
    }


    /**
     * Упрощенный вариант сохранения платежа
     * тут указываются только самые необходимые параметры
     *
     * @param id
     * @param userId
     * @param value
     * @param typeId
     * @param date
     * @param areaId
     * @param description
     * @param comment
     * @param callBack
     */
    static save(id, userId, value, typeId, date, areaId, description, comment, callBack) {
        if (value <= 0 || value == '' || value == undefined) {
            if (callBack != undefined) {
                callBack({error:{message: 'Сумма платежа не может быть меньше или равна нулю'}});
            }
            return false;
        }

        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'save',
                id: id,
                userId: userId,
                value: value,
                typeId: typeId,
                date: date,
                areaId: areaId,
                description: description,
                comment: comment
            },
            success: function(response) {
                if (callBack != undefined) {
                    callBack(response);
                }
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }


    /**
     * Добавление комментария к платежу
     *
     * @param paymentId
     * @param comment
     * @param callBack
     */
    static appendComment(paymentId, comment, callBack) {
        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'appendComment',
                paymentId: paymentId,
                comment: comment
            },
            success: function (response) {
                if (callBack != undefined) {
                    callBack(response);
                }
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }


    /**
     * Удаление комментария к платежу
     *
     * @param commentId
     * @param callBack
     */
    static removeComment(commentId, callBack) {
        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'removeComment',
                commentId: commentId
            },
            success: function (response) {
                if (callBack != undefined) {
                    callBack(response);
                }
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }


    /**
     * Удаление платежа
     *
     * @param paymentId
     * @param callBack
     */
    static remove(paymentId, callBack) {
        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'remove',
                paymentId: paymentId
            },
            success: function (response) {
                if (callBack != undefined) {
                    callBack(response);
                }
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }



    /**
     * Поиск кастомных типов платежей
     *
     * @param callBack
     */
    static getCustomTypesList(callBack) {
        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getCustomTypesList'
            },
            success: function (response) {
                if (callBack != undefined) {
                    callBack(response);
                }
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }

}