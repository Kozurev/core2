$(function(){
    $('body')

        //Открытие всплывающего окна редактирования скписка доп. свйоства
        .on('click', '.edit_property_list', function(e){
            e.preventDefault();
            var propertyId = $(this).data('prop-id');
            getPropertyListPopup(propertyId);
        })

        //Сохранение значения списка - создание / редактирование
        .on('click', '#property_list_save', function(e){
            e.preventDefault();

            var
                id =            $(this).data('id'),
                propId =        $(this).data('prop-id'),
                valueInput =    $('#property_list_value'),
                value =         valueInput.val(),
                saveBtn =       $('#property_list_save'),
                canselBtn =     $('.btn-cancel-block'),
                itemsList =     $('#property_list_select');

            canselBtn.css('display', 'none');

            savePropertyListValue(id, propId, value, function(response) {
                valueInput.val('');

                PropertyList.clearCache(propId);

                if(saveBtn.data('id') == 0) { //Создание нового элемента
                    var previousOptions = itemsList.html();
                    itemsList.html(
                        '<option value="' + response.id + '"' +
                        ' data-prop-id="' + response.propertyId + '">' +
                        response.value + '</option>'
                    );
                    itemsList.append(previousOptions);
                } else { //Редаткирование существующего
                    var
                        id =            saveBtn.data('id'),
                        editingOption = itemsList.find('option[value='+id+']');

                    saveBtn.data('id', '');
                    editingOption.text(response.value);
                }
            });
        })

        /**
         * Удаление выбранных элементов доп. свойства
         */
        .on('click', '#property_list_delete', function(e){
            e.preventDefault();
            var options = $('#property_list_select').find("option:selected");

            $('.btn-cancel-block').css('display', 'none');
            $('#property_list_save').data('id', 0);
            $('#property_list_value').val('');
            let propId = $(this).data('prop-id');

            $.each(options, function(key, option){
                deletePropertyListValue($(option).val(), function(response){
                    if(response != '') {
                        alert(response);
                    }
                    $(option).remove();
                    PropertyList.clearCache(propId);
                });
            });
        })

        /**
         * Редактирование элемента списка доп. свойства
         */
        .on('click', '#property_list_edit', function(e){
            e.preventDefault();

            var
                option =    $('#property_list_select').find("option:selected")[0],
                id =        $(option).val(),
                value =     $(option).text();

            $('.btn-cancel-block').css('display', 'inline-block');
            $('#property_list_save').data('id', id);
            $('#property_list_value').val(value);
        })

        /**
         * Отмена редактирования элемента доп свйоства
         */
        .on('click', '#property_list_cancel', function(e){
            e.preventDefault();
            $('#property_list_save').data('id', 0);
            $('#property_list_value').val('');
            $(this).hide();
        });

});


/**
 * Удаление элемента списка дополнительного свойства
 *
 * @param id
 * @param callback
 */
function deletePropertyListValue(id, callback)
{
    $.ajax({
        type: 'GET',
        url: root + '/',
        data: {
            ajax: 1,
            action: 'deletePropertyListValue',
            id: id
        },
        success: function(response) {
            if(typeof callback === 'function') {
                callback(response);
            }
        }
    });
}


/**
 * Создание/редактирование элемента списка дополнительного свойства
 *
 * @param id
 * @param propertyId
 * @param value
 * @param callback
 */
function savePropertyListValue(id, propertyId, value, callback)
{
    $.ajax({
        type: 'GET',
        url: root + '/',
        dataType: 'json',
        data: {
            ajax: 1,
            action: 'savePropertyListValue',
            id: id,
            prop_id: propertyId,
            value: value
        },
        success: function(response) {
            if(typeof callback === 'function') {
                callback(response);
            }
        }
    });
}


/**
 * Вывод всплывающего окна для работы со списком доп. свойства
 *
 * @param propertyId
 */
function getPropertyListPopup(propertyId)
{
    loaderOn();

    $.ajax({
        type: 'GET',
        url: root + '/',
        data: {
            ajax: 1,
            action: 'getPropertyListPopup',
            prop_id: propertyId
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        }
    });
}


/**
 * Сохранение значения дополнительного свойства объекта
 *
 * @param propName   - tag_name дополнительного свойства
 * @param value      - значение
 * @param modelName  - название объекта к которому задается значение
 * @param modelId    - id объекта к которому задается значение
 * @param func       - исполняемая функция после выполнения запроса
 */
function savePropertyValue(propName, value, modelName, modelId, func)
{
    $.ajax({
        type: 'GET',
        url: root + '/',
        data: {
            ajax: 1,
            action: 'savePropertyValue',
            prop_name: propName,
            value: value,
            model_name: modelName,
            model_id: modelId
        },
        success: function(response) {
            if(response != '') {
                notificationError('Ошибка: ' + response);
            }
            if(typeof func === 'function') {
                func(response);
            }
            loaderOff();
        }
    });
}


/**
 * Проверка наличия дополнительного свойства
 *
 * @param propName   - tag_name дополнительного свойства
 * @param modelName  - название объекта у которого ищется значение
 * @param modelId    - id объекта у которого ищется значение
 */
function checkPropertyValue(propName, modelName, modelId,callback)
{
  $.ajax({
        type: 'GET',
        url: root + '/',
        data: {
            ajax: 1,
            action: 'checkPropertyValue',
            prop_name: propName,
            model_name: modelName,
            model_id: modelId
        },
        success: callback
    });
}