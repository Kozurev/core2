// $(function() {
//     $('#myCalls_section').on('click', '#myCalls_api_url_save', function(e) {
//         e.preventDefault();
//         loaderOn();
//         savePropertyValue('my_calls_url', $('#myCalls_api_url').val(), 'User', $('#director_id').val(), function(response) {
//             notificationSuccess('API ссылка успешно сохранена');
//             loaderOff();
//         });
//     });
// });

$(function() {
    $(document).on('click', '.property_value_save', function(e) {
        e.preventDefault();
        loaderOn();
        let
            propName = $(this).data('property-name'),
            modelName = $(this).data('model-name'),
            objectId = $(this).data('object-id');
        savePropertyValue(propName, $('#' + propName).val(), modelName, objectId, function(response) {
            notificationSuccess('Данные успешно сохранены');
            loaderOff();
        });
    });
});