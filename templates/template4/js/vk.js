/**
 * Формирование модалки для создания/редактирования группы вк
 *
 * @param response
 */
function showVkGroupPopup(response) {
    if (response !== undefined && !checkResponseStatus(response)) {
        return false;
    }

    let popupData =
        '<div class="popup-row-block">' +
            '<form id="editVkGroupForm">' +
                '<div class="column"><span>Название</span><span style="color:red"> *</span></div>' +
                '<div class="column"><input type="text" name="title" class="form-control"></div>' +
                '<div class="column"><span>Ссылка</span><span style="color:red"> *</span></div>' +
                '<div class="column"><input type="text" name="link" class="form-control"></div>' +
                '<div class="column"><span>Ключ доступа</span></div>' +
                '<div class="column"><input type="text" name="secret_key" class="form-control"></div>' +
                '<div class="column"><span>Секретный ключ Callback API</span></div>' +
                '<div class="column"><input type="text" name="secret_callback_key" class="form-control"></div>' +
                '<input type="hidden" name="id">' +
            '</form>' +
        '</div>' +
        '<div class="row">' +
            '<button class="btn btn-default" onclick="saveVkGroupForm($(\'#editVkGroupForm\'))">Сохранить</button>' +
        '</div>';

    prependPopup(popupData);

    if (response !== undefined) {
        let popup = $('.popup');
        let group = response.group;
        popup.find('input[name=id]').val(group.id);
        popup.find('input[name=title]').val(group.title);
        popup.find('input[name=link]').val(group.link);
        popup.find('input[name=secret_key]').val(group.secret_key);
        popup.find('input[name=secret_callback_key]').val(group.secret_callback_key);
    }

    showPopup();
}


/**
 * Сохранения данных сообщества ВК с формы
 *
 * @param form
 */
function saveVkGroupForm(form) {
    loaderOn();
    let groupData = {};
    groupData.id = form.find('input[name=id]').val();
    groupData.title = form.find('input[name=title]').val();
    groupData.link = form.find('input[name=link]').val();
    groupData.secret_key = form.find('input[name=secret_key]').val();
    groupData.secret_callback_key = form.find('input[name=secret_callback_key]').val();
    Vk.save(groupData, function(response) {
        if (checkResponseStatus(response)) {
            window.location.reload();
        }
        loaderOff();
    });
}


/**
 * Перезагрузка после удаления сообщества
 *
 * @param response
 */
function removeVkGroupCallback(response) {
    if (checkResponseStatus(response)) {
        window.location.reload();
    }
}