

class Tarif {

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/tariff/index.php';
    };


    /**
     * Получение списка
     *
     * @param params
     * @param callBack
     */
    static getList(params, callBack) {
        $.ajax({
            type: 'GET',
            url: Tarif.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getList',
                params: params
            },
            success: function(response) {
                callBack(response);
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }


    /**
     * Покупка тарифа
     *
     * @param userId
     * @param tariffId
     * @param callBack
     */
    static buyTariff(userId, tariffId, callBack) {
        $.ajax({
            type: 'GET',
            url: Tarif.getApiLink(),
            dataType: 'json',
            data: {
                action: 'buyForClient',
                userId: userId,
                tariffId: tariffId
            },
            success: function(response) {
                callBack(response);
            },
            error: function () {
                notificationError('Произошла ошибка');
            }
        });
    }

    /**
     * Удаление тарифа
     *
     * @param tariffId
     * @param callback
     */
    static remove(tariffId, callback) {
        $.ajax({
            type: 'POST',
            url: Tarif.getApiLink(),
            dataType: 'json',
            data: {
                action: 'remove',
                tariffId: tariffId
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function (response) {
                showResponseNotification(response.responseJSON);
            }
        });
    }

}