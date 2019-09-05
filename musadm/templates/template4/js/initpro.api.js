class Initpro {

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/initpro/api.php';
    };


    /**
     * Отправка чека на кассы
     *
     * @param checkData
     * @param callback
     */
    static sendCheck(checkData, callback) {
        checkData.action = 'sendCheck';
        $.ajax({
            type: 'POST',
            url: Initpro.getApiLink(),
            dataType: 'json',
            data: checkData,
            success: function(response) {
                if (typeof callback == 'funciton') {
                    callback(response);
                }
            }
        });
    }

}