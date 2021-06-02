/**
 * Иннициализация AJAX-формы
 *
 * @param formSelector
 * @param successCallback
 * @param errorCallback
 */
function initAjaxForm(formSelector, successCallback, errorCallback) {
    $(document).on('submit', formSelector, function(e) {
        e.preventDefault();
        let $form = $(formSelector);
        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (typeof successCallback === 'function') {
                    successCallback(response);
                } else {
                    ajaxFormSuccessCallbackDefault(response);
                }
            },
            error: function (response) {
                if (typeof errorCallback === 'function') {
                    errorCallback(response);
                } else {
                    ajaxFormErrorCallbackDefault(response);
                }
            }
        });
    });
}

/**
 * Калбэк функция по умолчанию для положительного ответа
 *
 * @param response
 * @param afterModal
 */
function ajaxFormSuccessCallbackDefault(response, afterModal) {
    let status = response.status !== undefined
        ?   response.status === true || response.status === 'success' ? 'success' : 'error'
        :   'success';
    swal({
        type: status,
        title: response.message !== undefined ? response.message : '',
    }).then((result) => {
        if (typeof afterModal === 'function') {
            afterModal(result);
        }
    });
}

/**
 * Калбэк функция по умолчанию для ответа с ошибкой
 *
 * @param response
 */
function ajaxFormErrorCallbackDefault(response) {
    let message = '';
    if (response.responseJSON.errors !== undefined) {
        $.each(response.responseJSON.errors, function(key, error) {
            message += error + '; ';
        });
    } else {
        message = (response.responseJSON.message !== undefined) ? response.responseJSON.message : 'Server error 500';
    }
    swal.fire({
        type: 'error',
        title: message
    });
}

/**
 * Удаление наблюдателей для ранее иннициализированной AJAX формы
 *
 * @param formSelector
 */
function removeAjaxForm(formSelector) {
    $(document).off('submit', formSelector);
}