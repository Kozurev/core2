$(function() {
    $('#myCalls_section').on('click', '#myCalls_api_url_save', function(e) {
        e.preventDefault();
        loaderOn();
        savePropertyValue('my_calls_url', $('#myCalls_api_url').val(), 'User', $('#director_id').val(), function(response) {
            notificationSuccess('API ссылка успешно сохранена');
            loaderOff();
        });
    });
});