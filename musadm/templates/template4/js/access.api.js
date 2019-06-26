

class Access {

    /**
     * Абсолютный путь к api
     *
     * @returns {string}
     */
    static getApiLink () {
        return root + '/api/access/api.php';
    };


    /**
     * Получение информации о группе (для создания/редактирования)
     * параметр prentId необходим при создании группы
     *
     * @param id
     * @param parentId
     * @param callBack
     */
    static edit(id, parentId, callBack) {
        $.ajax({
            type: 'GET',
            url: Access.getApiLink(),
            dataType: 'json',
                data: {
                action: 'edit',
                id: id,
                parentId: parentId
            },
            success: function (response) {
                callBack(response);
            }
        });
    }


    /**
     * Сохранение информации о группе
     *
     * @param id
     * @param title
     * @param description
     * @param parentId
     * @param callBack
     */
    static save(id, title, description, parentId, callBack) {
        loaderOn();
        $.ajax({
            type: 'GET',
            url: Access.getApiLink(),
            dataType: 'json',
            data: {
                action: 'save',
                id: id,
                title: title,
                description: description,
                parentId: parentId
            },
            success: function (response) {
                callBack(response);
                loaderOff();
            },
            error: function() {
                notificationError('При сохранении данных группы произошла ошибка');
                loaderOff();
            }
        });
    }


    /**
     * Удаление группы
     *
     * @param id
     * @param callBack
     */
    static remove(id, callBack) {
        if (confirm('Вы действительно хотите удалить данную группу, а также все её дочерние группы?')) {
            loaderOn();
            $.ajax({
                type: 'GET',
                url: Access.getApiLink(),
                dataType: 'json',
                data: {
                    action: 'delete',
                    id: id
                },
                success: function (response) {
                    callBack(response);
                    loaderOff();
                },
                error: function () {
                    notificationError('При удалении группы прав доступа произошла ошибка');
                    loaderOff();
                }
            });
        }
    }


    /**
     * @param groupId
     * @param capabilityName
     */
    static capabilityAppend(groupId, capabilityName) {
        Access.setCapability(groupId, capabilityName, 1, function(response){
            notificationSuccess('Доступ "' + response.capability + '" для группы ' + response.group.title + ' разрешен');
        });
    }


    /**
     * @param groupId
     * @param capabilityName
     */
    static capabilityForbidden(groupId, capabilityName) {
        Access.setCapability(groupId, capabilityName, 0, function (response) {
            notificationSuccess('Доступ "' + response.capability + '" для группы ' + response.group.title + ' запрещен');
        });
    }


    /**
     * @param groupId
     * @param capabilityName
     */
    static capabilityAsParent(groupId, capabilityName) {
        Access.setCapability(groupId, capabilityName, -1, function (response) {
            notificationSuccess('Доступ "' + response.capability + '" для группы ' + response.group.title + ' как у родителя');
        });
    }


    /**
     * Изменение возможности для группы
     * при значении
     *  1: открывает доступ:
     *  0: закрывает доступ;
     *  -1: права доступа как у родителя
     *
     * @param groupId
     * @param capabilityName
     * @param value
     * @param callBack
     */
    static setCapability(groupId, capabilityName, value, callBack) {
        loaderOn();
        $.ajax({
            type: 'GET',
            url: Access.getApiLink(),
            dataType: 'json',
            data: {
                action: 'setCapability',
                groupId: groupId,
                capabilityName: capabilityName,
                value: value
            },
            success: function (response) {
                callBack(response);
                loaderOff();
            },
            error: function () {
                notificationError('При изменении прав доступа произошла ошибка');
                loaderOff();
            }
        });
    }


    /**
     * Формирование общего списка всех пользователей и списка пользователей, принадлежащих данной группе
     *
     * @param groupId
     * @param callBack
     * @param params
     */
    static getUserList(groupId, callBack, params) {
        $.ajax({
            type: 'GET',
            url: Access.getApiLink(),
            dataType: 'json',
            data: {
                action: 'getList',
                groupId: groupId,
                params: params
            },
            success: function (response) {
                callBack(response);
            }
        });
    }


    /**
     * Добавить пользователя в список группы
     *
     * @param groupId
     * @param userId
     * @param callBack
     */
    static appendUser(groupId, userId, callBack) {
        $.ajax({
            type: 'GET',
            url: Access.getApiLink(),
            dataType: 'json',
            data: {
                action: 'appendUser',
                groupId: groupId,
                userId: userId
            },
            success: function(response) {
                callBack(response);
            }
        });
    }


    /**
     * Удалить пользователя из списка группы
     *
     * @param groupId
     * @param userId
     * @param callBack
     */
    static removeUser(groupId, userId, callBack) {
        $.ajax({
            type: 'GET',
            url: Access.getApiLink(),
            dataType: 'json',
            data: {
                action: 'removeUser',
                groupId: groupId,
                userId: userId
            },
            success: function(response) {
                callBack(response);
            }
        });
    }

}