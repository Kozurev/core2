

class Payment {

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/payment/index.php';
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


    /**
     * Вызов всплывающего окна для оплаты через личный кабинет
     *
     * @returns {*}
     */
    static getSberApi() {
        if (typeof ipay === 'undefined') {
            notificationError('Пополнение баланса недоступно');
        } else {
            return ipayCheckout({
                    currency: 'RUB',
                    order_number: '',
                    description: 'Оплата музыкального обучения'
                },
                function(order) { Payment.sberApiSuccess(order) },
                function(order) { Payment.sberApiError(order) });
        }
    }


    /**
     * Создание платежа после оплаты клиентом и формирование чека
     *
     * @param order
     */
    static sberApiSuccess(order) {
        var
            clientId = $('#userid').val(),
            sum = order.formattedAmount,
            description = order.orderDescription,
            comment = 'Самостоятельное пополнение баланса клиентом. Номер платежа: ' + order.orderNumber;

        Payment.save(0, clientId, sum, 1, '', 0, description, comment, function(payment){
            var checkData = {};
            checkData.paymentId =   payment.id;
            checkData.userId =      payment.userId;
            checkData.userEmail =   order.email;
            checkData.description = payment.description;
            checkData.sum =         payment.value;
            Initpro.sendCheck(checkData, function(check) {
                window.location.reload();
            });
        });
    }

    static checkStatus(paymentId, callBack) {
        $.ajax({
            type: 'GET',
            url: Payment.getApiLink(),
            dataType: 'json',
            data: {
                action: 'checkStatus',
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

    static sberApiError(order) {
        console.log(order);
    }

}