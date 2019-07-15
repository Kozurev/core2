class PropertyList {

    /**
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/property_list/api.php';
    };


    /**
     * Формирование списка значений доп. свойства
     *
     * @param propertyId
     * @param callback
     */
    static getList(propertyId, callback) {
        $.ajax({
            type: 'GET',
            url: PropertyList.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getList',
                propertyId: propertyId
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

}