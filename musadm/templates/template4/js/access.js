function editAccessGroupCallback(response) {
    var popupData = '<div class="row" style="margin-top: 40px">' +
        '<div class="column">' +
            '<span>Название</span><span style="color:red">*</span>' +
        '</div>' +
        '<div class="column">' +
            '<input class="form-control" type="text" required value="'+response.title+'" id="title">' +
        '</div>' +
        '<hr>' +
        '<div class="column">' +
            '<span>Описание</span>' +
        '</div>' +
        '<div class="column">' +
            '<textarea id="description">'+response.description+'</textarea>' +
        '</div>' +
        '<button class="btn btn-default" onclick="Access.save(' +
            ''+response.id+', ' +
            '$(\'#title\').val(), ' +
            '$(\'#description\').val(), ' +
            response.parentId+', accessGroupSaveCallBack)">' +
        'Сохранить' +
        '</button>' +
        '</div>';
    showPopup(popupData);
    loaderOff();
}


/**
 * Колбэк функция, выполняемая при сохранении группы
 * Создает новую строку таблицы с новой группой (при создании) или обновляет информацию в таблице (при редактировании)
 *
 * @param response
 */
function accessGroupSaveCallBack(response) {
    closePopup();
    var tr = $('#group_' + response.id);
    if (tr.length == 1) {
        tr.find('.title').text(response.title);
        tr.find('.description').text(response.description);
    } else {
        tr = $('<tr id="group_'+response.id+'"></tr>');
        var
            tdMain = $('<td></td>'),
            tdCount = tdMain.clone(),
            tdActions = tdMain.clone();
        tdMain.append('<a class="title" href="'+root+'/access?parent_id='+response.parentId+'">'+response.title+'</a> (0)');
        tdMain.append('<p><small class="description">'+response.description+'</small></p>');
        tdCount.append('<span id="countUsers_'+response.id+'">'+response.countUsers+'</span>');
        if (response.parentId != 0) {
            tdActions.append('<a class="action associate" onclick="Access.getUserList('+response.id+', Access.userListCallBack)" title="Просмотреть/редактировать список полльзователей, принадлежащих группе"></a>');
        }
        tdActions.append('<a class="action edit" onclick="Access.edit('+response.id+')"></a>');
        tdActions.append('<a class="action settings" href="'+root+'/access?group_id='+response.id+'"></a>');
        if (response.parentId != 0) {
            tdActions.append('<a class="action delete" onclick="Access.remove('+response.id+',Access.removeCallBack)"></a>');
        }
        tr.append(tdMain);
        tr.append(tdCount);
        tr.append(tdActions);
        $('table').append(tr);
        notificationSuccess('Группа прав доступа ' + response.title + ' была успешно сохранена');
    }
}


/**
 * Формирование содержимого всплывающего окна редактирования состава группы
 *
 * @param response
 * @returns {boolean}
 */
function acessUserListCallBack(response) {
    if (typeof response.error !== 'undefined') {
        notificationError('Ошибка ' + response.error.code + ': ' + response.error.message);
        return false;
    }

    var popupData = $(
        '<div class="row popup-row-block" id="accessGroupAssignments">' +
            '<div class="col-lg-12">' +
                '<h4>'+response.group.title+'</h4>' +
            '</div>' +
            '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">' +
                '<div class="row">' +
                    '<div class="col-md-9">' +
                        '<input type="text" id="mainUserQuery" class="form-control" placeholder="Фамилия">' +
                    '</div>' +
                    '<div class="col-md-3">' +
                        '<a href="#" class="btn btn-blue" onclick="' +
                            'User.getList({' +
                                'filter: {surname: $(\'#mainUserQuery\').val()},' +
                                'active: 1,' +
                                'select: [\'id\', \'surname\', \'name\', \'group_id\'],' +
                                'groups: [2, 4, 5, 6],' +
                                'order: {group_id: \'ASC\', surname: \'ASC\'}' +
                                '}, function(users){ ' +
                                    'var mainUserList = $(\'#mainUserList\'); mainUserList.empty();' +
                                    '$.each(users, function(key, user){' +
                                        'var option = \'<option value=\'+user.id+\'>\'+user.surname+ \' \' + user.name + \'</option>\';' +
                                        'mainUserList.append(option);' +
                                    '});' +
                                '})' +
                        '">Поиск</a>' +
                    '</div>' +
                '</div>' +
                '<div class="row">' +
                    '<select class="form-control" id="mainUserList" multiple="multiple" size="7"></select>' +
                '</div>' +
                '<div class="row text-center">' +
                    '<a href="#" class="btn btn-large btn-green" ' +
                        'onclick="accessGroupAppendUsers('+response.group.id+', $(\'#mainUserList\').val())">Добавить</a>' +
                '</div>' +
            '</div>' +
            '<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">' +
                '<div class="row">' +
                    '<div class="col-md-9">' +
                        '<input type="text" id="groupUserQuery" class="form-control" placeholder="Фамилия">' +
                    '</div>' +
                    '<div class="col-md-3">' +
                        '<a class="btn btn-blue" onclick="' +
                            'Access.getUserList('+response.group.id+', function(users){' +
                                'var groupUserList = $(\'#groupUserList\'); groupUserList.empty();' +
                                '$.each(users.users, function(key, user){' +
                                    'var option = \'<option value=\'+user.id+\'>\'+user.surname+ \' \' + user.name + \'</option>\';' +
                                    'groupUserList.append(option);' +
                                '});' +
                            '}, {filter: {surname: $(\'#groupUserQuery\').val()}})">Поиск</a>' +
                    '</div>' +
                '</div>' +
                '<div class="row">' +
                    '<select class="form-control" id="groupUserList" multiple="multiple" size="7"></select>' +
                '</div>' +
                '<div class="row text-center">' +
                    '<a href="#" class="btn btn-large btn-red" ' +
                        'onclick="accessGrouRemoveUsers('+response.group.id+', $(\'#groupUserList\').val())">Удалить</a>' +
                '</div>' +
            '</div>' +
        '</div>');

    //Формирование общего списка пользователей
    var mainUserList = popupData.find('#mainUserList');
    User.getList(
        {
            active: 1,
            select: ['id', 'surname', 'name', 'group_id'],
            groups: [2, 4, 5, 6],
            order: {
                group_id: 'ASC',
                surname: 'ASC'
            }
        },
        function(response) {
            $.each(response, function(key, user){
                mainUserList.append('<option value="'+user.id+'">' + user.surname + ' ' + user.name + '</option>');
            });
        }
    );

    //Формирование списка пользователей, принадлежащих группе
    var groupUserList = popupData.find('#groupUserList');
    $.each(response.users, function(key, user){
        groupUserList.append('<option value="'+user.id+'">' + user.surname + ' ' + user.name + '</option>');
    });

    showPopup(popupData);
    return true;
}


/**
 * Колбэк функция, выполняемая при удалении группы
 * Удаляет строку в таблице с удаленной группой
 *
 * @param response
 */
function accessGroupRemoveCallback(response) {
    if (response.parentId == 0) {
        notificationError('Ошибка: удаление основной группы ' + response.title + ' невозможно');
    } else {
        notificationSuccess('Группа ' + response.title + ' была удалена');
        $('#group_' + response.id).remove();
    }
}



/**
 * @param groupId
 * @param selectedUsers
 */
function accessGroupAppendUsers(groupId, selectedUsers) {
    //var selectedUsers = select.val();
    if (selectedUsers == null) {
        return false;
    }

    //список пользователей группы
    var groupSelect = $('#groupUserList');
    //элемент содержащий количество пользователей, принадлежащих группе
    var groupCountUsers = $('#countUsers_' + groupId);

    $.each(selectedUsers, function(key, id){
        Access.appendUser(groupId, id, function(response) {
            if (typeof response.error !== 'undefined') {
                notificationError('Ошибка ' + response.error.code + ': ' + response.error.message);
                return false;
            } else {
                groupSelect.append('<option value="'+id+'">'+response.user.surname + ' ' + response.user.name +'</option>');
                groupCountUsers.text(Number(groupCountUsers.text()) + 1);
            }
        });
    });
}


/**
 * @param groupId
 * @param selectedUsers
 */
function accessGrouRemoveUsers(groupId, selectedUsers) {
    //var selectedUsers = select.val();
    if (selectedUsers == null) {
        return false;
    }

    //список пользователей группы
    var groupSelect = $('#groupUserList');
    //элемент содержащий количество пользователей, принадлежащих группе
    var groupCountUsers = $('#countUsers_' + groupId);

    $.each(selectedUsers, function(key, id){
        Access.removeUser(groupId, id, function(response) {
            if (typeof response.error !== 'undefined') {
                notificationError('Ошибка ' + response.error.code + ': ' + response.error.message);
                return false;
            } else {
                groupSelect.find('option[value='+id+']').remove();
                groupCountUsers.text(Number(groupCountUsers.text()) - 1);
            }
        });
    });
}