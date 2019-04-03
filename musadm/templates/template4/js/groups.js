"use strict";

var root = $('#rootdir').val();


$(function(){
    $('body')
        .on('submit', '#groupSearch', function(e) {
            e.preventDefault();
            loaderOn();
            var userQuery = $('#groupUserQuery');
            var surname = userQuery.val();
            // if (surname === '') {
            //     userQuery.addClass('error');
            //     loaderOff();
            //     return false;
            // } else {
                // userQuery.removeClass('error');
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

            $.each(selectedUsers, function (key, user) {
                var userId = $(user).val();
                if ($.inArray(userId, existingAsgmIds) === -1) {
                    selectedUsersIds.push(userId);
                }
            });

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


function refreshGroupTable() {
    $.ajax({
        type: 'GET',
        url: root + '/groups',
        async: false,
        data: {
            action: 'refreshGroupTable',
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
        url: "groups",
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