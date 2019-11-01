class Schedule {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/schedule/index.php';
    };


    static clearCache() {
        localStorage.removeItem('schedule.getAreasList.areas');
        localStorage.removeItem('schedule.getAreasList.lastParams');
    }


    /**
     * Проверка на существование периода отсутствия у клиента на определенную дату
     *
     * @param params
     * @param callBack
     */
    static checkAbsentPeriod(params, callBack) {
        params.action = 'checkAbsentPeriod';
        $.ajax({
            type: 'GET',
            url: Schedule.getApiLink(),
            dataType: 'json',
            data: params,
            success: function(response) {
                callBack(response);
            }
        });
    }


    /**
     * Получение списка филлиалов
     *
     * @param params
     * @param callBack
     */
    static getAreasList(params, callBack) {
        params.action = 'getAreasList';
        let cache = localStorage.getItem('schedule.getAreasList.areas');
        let lastCacheParams = localStorage.getItem('schedule.getAreasList.lastParams');

        if (JSON.stringify(params) == lastCacheParams && cache != null) {
            callBack(JSON.parse(cache));
        } else {
            $.ajax({
                type: 'GET',
                url: Schedule.getApiLink(),
                async: false,
                dataType: 'json',
                data: params,
                success: function(response) {
                    localStorage.setItem('schedule.getAreasList.lastParams', JSON.stringify(params));
                    localStorage.setItem('schedule.getAreasList.areas', JSON.stringify(response));
                    callBack(response);
                }
            });
        }
    }


    /**
     * Сохранение периода отсутствия
     *
     * @param absent
     * @param callback
     */
    static saveAbsentPeriod(absent, callback) {
        absent.action = 'saveAbsentPeriod';
        $.ajax({
            type: 'POST',
            url: Schedule.getApiLink(),
            dataType: 'json',
            data: absent,
            success: function(response) {
                if (typeof callback == 'function') {
                    callback(response);
                }
            },
            error: function(response) {
                checkResponseStatus(response);
            }
        });
    }
}