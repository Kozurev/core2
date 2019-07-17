class Schedule {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/schedule/api.php';
    };



    /**
     * Проверка на существование периода отсутствия у клиента на определенную дату
     *
     * @param userId
     * @param date
     * @param callBack
     */
    static checkAbsentPeriod(userId, date, callBack) {
        $.ajax({
            type: 'GET',
            url: Schedule.getApiLink(),
            dataType: 'json',
            data: {
                action: 'checkAbsentPeriod',
                userId: userId,
                date: date
            },
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
}