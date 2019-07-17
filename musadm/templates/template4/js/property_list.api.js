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
        let cache = localStorage.getItem('propertyList.getList_' + propertyId);
        if (cache == null) {
            $.ajax({
                type: 'GET',
                url: PropertyList.getApiLink(),
                dataType: 'json',
                data: {
                    action: 'getList',
                    propertyId: propertyId
                },
                success: function (response) {
                    if (response.error == undefined && response.status != false) {
                        localStorage.setItem('propertyList.getList_' + propertyId, JSON.stringify(response));
                    }
                    if (typeof callback == 'function') {
                        callback(response);
                    }
                },
                error: function () {
                    notificationError('Произзошла ошибка');
                }
            });
        } else {
            if (typeof callback == 'function') {
                callback(JSON.parse(cache));
            }
        }

    }

}