

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

}