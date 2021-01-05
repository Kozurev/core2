$(function() {

});

function refreshCheckoutsTable() {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/integration/checkouts',
        data: {
            action: 'refreshPage'
        },
        dataType: 'html',
        success: function (response) {
            $('.page').html(response);
        },
        complete: function() {
            loaderOff();
        }
    });
}

function makeCheckoutModal(id) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/integration/checkouts',
        data: {
            action: 'getCheckoutModal',
            id: id
        },
        dataType: 'html',
        success: function (response) {
            showPopup(response);
        },
        error: function(response) {
            notificationError('При создании/редактировании кассы произошла неизвестная ошибка');
        },
        complete: function() {
            loaderOff();
        }
    });
}