$(function(){

    $('body')
        .on('click', '.edit_property_list', function(e){
            e.preventDefault();
            var propertyId = $(this).data('prop-id');
            getPropertyListPopup(propertyId);
        })
        .on('click', '#property_list_save', function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var propId = $(this).data('prop-id');
            var value = $('#property_list_value').val();

            $('.btn-cancel-block').css('display', 'none');

            savePropertyListValue(id, propId, value, function(response){
                var select = $('#property_list_select');

                $('#property_list_value').val('');

                //Создание нового элемента
                if($('#property_list_save').data('id') == 0)
                {
                    var previousOptions = select.html();
                    select.html(
                        '<option value="' + response.id + '"' +
                        ' data-prop-id="' + response.propertyId + '">' +
                        response.value + '</option>'
                    );
                    select.append(previousOptions);
                }
                //Редаткирование предыдущего
                else
                {
                    var id = $('#property_list_save').data('id');

                    $('#property_list_save').data('id', 0);

                    var editingOption = select.find('option[value='+id+']');
                    editingOption.text(response.value);
                }
            });
        })
        .on('click', '#property_list_delete', function(e){
            e.preventDefault();
            var options = $('#property_list_select').find("option:selected");

            $('.btn-cancel-block').css('display', 'none');
            $('#property_list_save').data('id', 0);
            $('#property_list_value').val('');

            $.each(options, function(key, option){
                deletePropertyListValue($(option).val(), function(response){
                    if (response != '')
                    {
                        alert(response);
                    }

                    $(option).remove();
                });
            });
        })
        .on('click', '#property_list_edit', function(e){
            e.preventDefault();
            var option = $('#property_list_select').find("option:selected")[0];
            var id = $(option).val();
            var value = $(option).text();

            $('.btn-cancel-block').css('display', 'inline-block');
            $('#property_list_save').data('id', id);
            $('#property_list_value').val(value);
        })
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
        success: function(response){
            callback(response);
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
        success: function(response){
            callback(response);
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
        success: function(response){
            showPopup(response);
            loaderOff();
        }
    });
}


/**
 * Сохранение значения дополнительного свойства объекта
 *
 * @param prop_name  - tag_name дополнительного свойства
 * @param value      - значение
 * @param model_name - название объекта к которому задается значение
 * @param model_id   - id объекта к которому задается значение
 * @param func       - исполняемая функция после выполнения запроса
 */
function savePropertyValue( prop_name, value, model_name, model_id, func )
{
    $.ajax({
        type: "GET",
        url: root + "/",
        data: {
            ajax: 1,
            action: "savePropertyValue",
            prop_name: prop_name,
            value: value,
            model_name: model_name,
            model_id: model_id
        },
        success: function(responce){
            if( responce != "" )    alert("Ошибка: " + responce);
            if(typeof func === "function") func();
            loaderOff();
        }
    });
}