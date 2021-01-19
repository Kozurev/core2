"use strict";

var root = $('#rootdir').val();


$(function(){
    $('body')
        .on("click", ".filial_show", function(e){
            loaderOn();
            let dateFrom = $('input[name=date_from]').val();
            let dateTo = $('input[name=date_to]').val();
            let areaId = $('select[name=area_id]').val();
            showGroupHistory(dateFrom, dateTo, areaId);
        })
        .on('submit', '#groupSearch', function(e) {
            e.preventDefault();
            loaderOn();
            var userQuery = $('#groupUserQuery');
            var surname = userQuery.val();
                groupSearchClient(surname, function(response) {
                    var clientsList = $('#groupUserList');
                    clientsList.empty();
                    $.each(response, function(key, user) {
                        clientsList.append('<option value="' + user.id + '">' + user.fio + '</option>');
                    });
                    loaderOff();
                });
            // }
        })
        .on('click', '#groupAppendClient', function(e) {
            e.preventDefault();
            loaderOn();

            var
                groupId = $('#groupId').val(),
                usersList = $('#groupUserList'),
                asgmList = $('#groupUserAssignments'),
                selectedUsers = usersList.find('option:selected'),
                existingAsgm = asgmList.find('option'),
                selectedUsersIds = [],
                existingAsgmIds = [];

            $.each(existingAsgm, function (key, user) {
                existingAsgmIds.push($(user).val());
            });

            if (selectedUsers.length > 0) {
                $.each(selectedUsers, function (key, user) {
                    var userId = $(user).val();
                    if ($.inArray(userId, existingAsgmIds) === -1) {
                        selectedUsersIds.push(userId);
                    }
                });
            } else if (usersList.val() > 0) {
                if ($.inArray(usersList.val(), existingAsgmIds) === -1) {
                    selectedUsersIds.push(usersList.val());
                }
            }

            groupCreateAssignments(groupId, selectedUsersIds, function(response) {
                $.each(response, function(key, user) {
                    asgmList.append('<option value="' + user.id + '">' + user.fio + '</option>');
                });
                refreshGroupTable();
            });
        })
        .on('click', '#groupRemoveClient', function(e) {
            e.preventDefault();
            loaderOn();

            var
                groupId = $('#groupId').val(),
                asgmList = $('#groupUserAssignments'),
                selectedUsers = asgmList.find('option:selected'),
                selectedUsersIds = [];

            $.each(selectedUsers, function(key, user) {
                selectedUsersIds.push($(user).val());
            });

            groupDeleteAssignments(groupId, selectedUsersIds, function(response) {
                $.each(response, function(key, user) {
                    asgmList.find('option[value='+user.id+']').remove();
                });
                refreshGroupTable();
            });
        })
        .on('change', '.group-checkbox', function(e) {
            var groupClients = $(this).parent().find('.group-list').find('input[type=checkbox]');
            var isGroupChecked;

            if ($(this).is(':checked')) {
                isGroupChecked = true;
            } else {
                isGroupChecked = false;
            }

            $.each(groupClients, function(key, input) {
                $(input).prop('checked', isGroupChecked);
            });
        })
        .on('click', '.show-group-users', function(e) {
            e.preventDefault();
            var clientsList = $(this).parent().find('.group-list');

            if (clientsList.css('display') == 'none') {
                clientsList.show();
            } else {
                clientsList.hide();
            }
        });
});


function groupCreateAssignments(groupId, userIds, callBack) {
    $.ajax({
        type: 'GET',
        url: root + '/groups',
        dataType: 'json',
        data: {
            action: 'groupCreateAssignments',
            groupId: groupId,
            userIds: userIds
        },
        success: function(response) {
            callBack(response);
        }
    });
}


function groupDeleteAssignments(groupId, userIds, callback) {
    $.ajax({
        type: 'GET',
        url: root + '/groups',
        dataType: 'json',
        data: {
            action: 'groupDeleteAssignments',
            groupId: groupId,
            userIds: userIds
        },
        success: function(response) {
            callback(response);
        }
    });
}


function groupSearchClient(surname, callBack) {
    $.ajax({
        type: 'GET',
        url: root + '/groups',
        dataType: 'json',
        data: {
            action: 'groupSearchClient',
            surname: surname
        },
        success: function(response) {
            callBack(response);
        }
    });
}


function refreshGroupTable(page) {
    if (page === undefined || page < 1) {
        page = 1;
    }
    $.ajax({
        type: 'GET',
        // url: root + '/groups',
        url: '',
        // async: false,
        data: {
            action: 'refreshGroupTable',
            page: page
        },
        success: function(response) {
            $('.page').html(response);
            $('#sortingTable').tablesorter();
            loaderOff();
        },
        error: function(response) {
            notificationError('Произошла ошибка при обновлении');
            loaderOff();
        }
    });
}

function getGroupPopup(groupId) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "updateForm",
            group_id: groupId
        },
        success: function(response) {
            showPopup(response);
        }
    });
}

function getGroupComposition(groupId) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/groups',
        data: {
            action: 'getGroupComposition',
            group_id: groupId
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        }
    });
}
function showGroupHistory(periodFrom, periodTo, areaId) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'show',
            date_from: periodFrom,
            date_to: periodTo,
            area_id: areaId
        },
        success: function(response) {
            let data = $(response).find('#sortingTable');
            $('#sortingTable').replaceWith(data);
            console.log(periodFrom, periodTo, areaId);
            loaderOff();
        }
    });
}