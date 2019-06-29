class Group {
    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/groups/api.php';
    };


    /**
     * Ормирвание спика групп
     *
     * @param params
     * @param callBack
     */
    static getList(params, callBack) {
        $.ajax({
            type: 'GET',
            url: Group.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getList',
                params: params
            },
            success: function (response) {
                if (typeof callBack == 'function') {
                    callBack(response);
                }
            },
            error: function () {
                notificationError('Произзошла ошибка');
            }
        });
    }

}