class MyCalls
{
    static getApiLink() {
        return root + '/api/myCalls/index.php';
    }

    static makeCall(fromUserId, toPhoneNUmber, callback) {
        $.ajax({
            type: 'POST',
            url: MyCalls.getApiLink(),
            dataType: 'json',
            data: {
                action: 'makeCall',
                fromUserId: fromUserId,
                toPhoneNumber: toPhoneNUmber
            },
            success: function(response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            },
            error: function (response1, response2) {
                console.log(response1, response2);
            }
        });
    }

}