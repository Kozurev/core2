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
}