class Senler
{
    static getApiLink() {
        return root +  '/api/senler/index.php';
    }

    /**
     * Поиск всех групп подписки из сенлера
     *
     * @param params
     * @param callback
     */
    static getSubscriptions(params, callback) {
        if (params === null) {
            params = {};
        }
        params.action = 'getGroups';
        $.ajax({
            type: 'GET',
            url: Senler.getApiLink(),
            dataType: 'json',
            data: params,
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При получении данных группы сенлера произошла неизвестная ошибка');
            }
        });
    }

    /**
     * Поиск настройки интеграции по айди
     *
     * @param id
     * @param callback
     */
    static getSetting(id, callback) {
        $.ajax({
            type: 'GET',
            url: Senler.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getSetting',
                id: id
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При получении данных настроек интеграции произошла неизвестная ошибка');
            }
        });
    }

    /**
     * Сохранение настройки интеграции сенлера
     *
     * @param data
     * @param callback
     */
    static saveSetting(data, callback) {
        data.action = 'saveSetting';
        $.ajax({
            type: 'POST',
            url: Senler.getApiLink(),
            dataType: 'json',
            data: data,
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При сохранении данных настройки произошла неизвестная ошибка');
            }
        });
    }

    /**
     * Удаление одной из настроек интеграции
     *
     * @param id
     * @param callback
     */
    static deleteSetting(id, callback) {
        $.ajax({
            type: 'GET',
            url: Senler.getApiLink(),
            dataType: 'json',
            data: {
                action: 'deleteSetting',
                id: id
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При удалении данных настроек интеграции произошла неизвестная ошибка');
            }
        });
    }

}