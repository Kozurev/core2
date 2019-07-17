class Lids {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/lids/api.php';
    };


    /**
     * Получение информации о лиде
     *
     * @param id
     * @param callback
     */
    static getLid(id, callback) {
        $.ajax({
            type: 'GET',
            url: Lids.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getLid',
                id: id
            },
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка');
            }
        });
    }


    /**
     * Получение списка статусов лида
     *
     * @param callback
     */
    static getStatusList(callback) {
        let cache = localStorage.getItem('lids.getStatusList');
        if (cache == null) {
            $.ajax({
                type: 'GET',
                url: Lids.getApiLink(),
                dataType: 'json',
                data: {
                    action: 'getStatusList'
                },
                success: function (response) {
                    if (response.error == undefined && response.status != false) {
                        localStorage.setItem('lids.getStatusList', JSON.stringify(response));
                    }
                    if (typeof callback == 'function') {
                        callback(response);
                    }
                },
                error: function () {
                    notificationError('Произзошла ошибка во время поиска статусов лидов');
                }
            });
        } else {
            if (typeof callback == 'function') {
                callback(JSON.parse(cache));
            }
        }
    }


    /**
     * Метод-затычка для получения списка приоритетов на тот случай если он превратиться из статического в динамический
     *
     * @param callback
     */
    static getPriorityList(callback) {
        if (typeof callback == 'function') {
            callback([
                {id: 1, title: 'Низкий'},
                {id: 2, title: 'Средний'},
                {id: 3, title: 'Высокий'}
            ]);
        }
    }


    /**
     * Сохранение данных лида
     * набор данных лида представлен в виде объекта
     *
     * @param lid
     * @param callback
     */
    static save(lid, callback) {
        lid.action = 'save';
        $.ajax({
            type: 'POST',
            url: Lids.getApiLink(),
            data: lid,
            dataType: 'json',
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка во время сохранения лида');
            }
        });
    }


    /**
     * Изменение даты контроля лида
     *
     * @param lidId
     * @param date
     * @param callback
     */
    static changeDate(lidId, date, callback) {
        $.ajax({
            type: 'GET',
            url: Lids.getApiLink(),
            dataType: 'json',
            data: {
                action: 'changeDate',
                id: lidId,
                date: date
            },
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка во время изменения даты контроля лида');
            }
        });
    }


    /**
     * Изменение статуса лида
     *
     * @param lidId
     * @param statusId
     * @param callback
     */
    static changeStatus(lidId, statusId, callback) {
        $.ajax({
            type: 'GET',
            url: Lids.getApiLink(),
            dataType: 'json',
            data: {
                action: 'changeStatus',
                id: lidId,
                statusId: statusId
            },
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка во время изменения статуса лида');
            }
        });
    }


    /**
     * Изменение приоритета лида
     *
     * @param lidId
     * @param priorityId
     * @param callback
     */
    static changePriority(lidId, priorityId, callback) {
        $.ajax({
            type: 'GET',
            url: Lids.getApiLink(),
            dataType: 'json',
            data: {
                action: 'changePriority',
                id: lidId,
                priorityId: priorityId
            },
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка во время изменения статуса лида');
            }
        });
    }


    /**
     * Изменение филиала лида
     *
     * @param lidId
     * @param areaId
     * @param callback
     */
    static changeArea(lidId, areaId, callback) {
        $.ajax({
            type: 'GET',
            url: Lids.getApiLink(),
            dataType: 'json',
            data: {
                action: 'changeArea',
                id: lidId,
                areaId: areaId
            },
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка во время изменения статуса лида');
            }
        });
    }


    /**
     * Сохранение текста комментария к лиду
     *
     * @param commentId
     * @param lidId
     * @param text
     * @param callback
     */
    static saveComment(commentId, lidId, text, callback) {
        $.ajax({
            type: 'POST',
            url: Lids.getApiLink(),
            dataType: 'json',
            data: {
                action: 'saveComment',
                commentId: commentId,
                lidId: lidId,
                text: text
            },
            success: function (response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка во время изменения статуса лида');
            }
        });
    }

}