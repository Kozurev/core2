class Vk
{

    /**
     * @returns {string}
     */
    static getApiLink() {
        return root + '/api/vk/index.php';
    }


    /**
     * Получение данных группы
     *
     * @param id
     * @param callback
     */
    static getGroup(id, callback) {
        $.ajax({
            type: 'GET',
            url: Vk.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getGroup',
                id: id
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('При получении данных сообщетсва вк произошла неизвестная ошибка');
                loaderOff();
            }
        });
    }


    /**
     * Сохранение данных сообщества
     *
     * @param data
     * @param callback
     */
    static save(data, callback) {
        data.action = 'saveVkGroup';
        $.ajax({
            type: 'POST',
            url: Vk.getApiLink(),
            dataType: 'json',
            data: data,
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При сохранении данных сообщества вк произошла неизвестная ошибка');
                loaderOff();
            }
        });
    }


    /**
     * Удаление сообщества вк
     *
     * @param id
     * @param callback
     */
    static remove(id, callback) {
        $.ajax({
            type: 'POST',
            url: Vk.getApiLink(),
            dataType: 'json',
            data: {
                id: id,
                action: 'removeVkGroup'
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function() {
                notificationError('При удалении сообщества вк произошла неизвестная ошибка');
                loaderOff();
            }
        });
    }

}