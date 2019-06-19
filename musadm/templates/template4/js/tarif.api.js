

class Tarif {

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/tarif/api.php';
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
     * @param tarifId
     * @param callBack
     */
    static buyTarif(userId, tarifId, callBack) {
        $.ajax({
            type: 'GET',
            url: Tarif.getApiLink(),
            dataType: 'json',
            data: {
                action: 'buyForClient',
                userId: userId,
                tarifId: tarifId
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