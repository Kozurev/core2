$(function() {
    $(document).on('change', '#settingTypeSelect', function() {
        let lidStatusesList = $('.lid_status_id');
        let otherStatus = $('.other_status');

        if ($(this).val() == 1) {
            lidStatusesList.show();
            otherStatus.hide();
        } else {
            lidStatusesList.hide();
            lidStatusesList.find('select').val(0);
            otherStatus.show();
        }
    });
});

/**
 * Формирование всплывающего окна для создания/редактирования настроек интеграции с сенлером
 *
 * @param response
 * @returns {void}
 */
function showSenlerSettingPopup(response) {
    if (typeof response == 'object' && !checkResponseStatus(response)) {
        return;
    }

    loaderOn();

    let popupData =
        '<div class="popup-row-block">' +
            '<form id="editSenlerSettingForm">' +
                '<div class="column"><span>Тип</span></div>' +
                '<div class="column"><select id="settingTypeSelect" name="type" class="form-control">' +
                '<option value="1">Статус лида</option>' +
                '<option value="2">Другое</option>' +
                '</select></div>' +
                '<div class="column lid_status_id"><span>Статус лида</span><span style="color:red"> *</span></div>' +
                '<div class="column lid_status_id"><select name="lid_status_id" class="form-control"></select></div>' +
                '<div class="column other_status" style="display: none"><span>Другое</span><span style="color:red"> *</span></div>' +
                '<div class="column other_status" style="display: none"><select name="other_status" class="form-control"><option value="1">Архивация клиента</option></select></div>' +
                '<div class="column"><span>Группа подписки</span><span style="color:red"> *</span></div>' +
                '<div class="column"><select name="senler_subscription_id" class="form-control"></select></div>' +
                '<div class="column"><span>Филиал</span><span style="color:red"> *</span></div>' +
                '<div class="column"><select name="area_id" class="form-control"></select></div>' +
                '<div class="column"><span>Направление подготовки</span></div>' +
                '<div class="column"><select name="training_direction_id" class="form-control"><option value="0">Все</option></select></div>' +
                '<input type="hidden" name="vk_group_id" >' +
                '<input type="hidden" name="id">' +
            '</form>' +
        '</div>' +
        '<div class="row">' +
        '<button class="btn btn-default" onclick="loaderOn(); saveSenlerSettingFrom($(\'#editSenlerSettingForm\'), saveSenlerSettingCallback)">Сохранить</button>' +
        '</div>';

    prependPopup(popupData);

    var
        popup = $('.popup'),
        id =                        popup.find('input[name=id]'),
        type =                      popup.find('select[name=type]'),
        vkGroupId =                 popup.find('input[name=vk_group_id]'),
        areasSelect =               popup.find('select[name=area_id]'),
        lidStatusesSelect =         popup.find('select[name=lid_status_id]'),
        trainingDirectionSelect =   popup.find('select[name=training_direction_id]'),
        senlerSubscriptionIdSelect= popup.find('select[name=senler_subscription_id]'),
        selectedAreaId =            0,
        selectedLidStatus =         0,
        selectedOtherStatus =       0,
        selectedSubscriptionId =    0,
        selectedVkGroupId =         0,
        selectedTrainingDirectionId=0;

    if (typeof response == 'object') {
        id.val(response.id);
        selectedAreaId = response.area_id;
        selectedVkGroupId = response.vk_group_id;
        selectedLidStatus = response.lid_status_id;
        selectedOtherStatus = response.other_status;
        selectedSubscriptionId = response.senler_subscription_id;
        selectedTrainingDirectionId = response.training_direction_id;
    } else {
        selectedVkGroupId = response;
    }

    if (selectedOtherStatus > 0) {
        type.val(2);
    }

    vkGroupId.val(selectedVkGroupId);

    Lids.clearCache();
    Lids.getStatusList(function(statuses) {
        if (!checkResponseStatus(statuses)) {
            closePopup();
            loaderOff();
        } else {
            let isSelected = '';
            lidStatusesSelect.append('<option value="0"> ... </option>');
            $.each(statuses, function (key, status) {
                if (status.id == selectedLidStatus) {
                    isSelected = 'selected';
                } else {
                    isSelected = '';
                }
                lidStatusesSelect.append('<option value="'+status.id+'" '+isSelected+'>'+status.title+'</option>');
            });
            PropertyList.clearCache(20);
            PropertyList.getList(20, function(instruments) {
                if (!checkResponseStatus(instruments)) {
                    closePopup();
                    loaderOff();
                } else {
                    let isSelected = '';
                    $.each(instruments, function (key, instrument) {
                        if (instrument.id == selectedTrainingDirectionId) {
                            isSelected = 'selected';
                        } else {
                            isSelected = '';
                        }
                        trainingDirectionSelect.append('<option value="' + instrument.id + '" ' + isSelected + '>' + instrument.value + '</option>');
                    });

                    Senler.getSubscriptions({ group_id: selectedVkGroupId }, function(response) {
                        if (!checkResponseStatus(response)) {
                            closePopup();
                            loaderOff();
                        } else {
                            let isSelected = '';
                            $.each(response.subscriptions, function (key, subscription) {
                                if (subscription.subscription_id == selectedSubscriptionId) {
                                    isSelected = 'selected';
                                } else {
                                    isSelected = '';
                                }
                                senlerSubscriptionIdSelect.append('<option value="' + subscription.subscription_id + '" ' + isSelected + '>' + subscription.name + '</option>');
                            });

                            Schedule.getAreasList({}, function(areas) {
                                let isSelected = '';
                                $.each(areas, function (key, area) {
                                    if (area.id == selectedAreaId) {
                                        isSelected = 'selected';
                                    } else {
                                        isSelected = '';
                                    }
                                    areasSelect.append('<option value="' + area.id + '" ' + isSelected + '>' + area.title + '</option>');
                                });

                                showPopup();
                                loaderOff();
                            });
                        }
                    });
                }
            });
        }
    });
}


/**
 * Сохранение данных настройки интеграции сенлера
 *
 * @param form
 * @param callback
 */
function saveSenlerSettingFrom(form, callback) {
    var
        data =                  {},
        id =                    form.find('input[name=id]'),
        vkGroupId =             form.find('input[name=vk_group_id]'),
        areaId =                form.find('select[name=area_id]'),
        otherStatus =           form.find('select[name=other_status]'),
        lidStatuses =           form.find('select[name=lid_status_id]'),
        trainingDirectionId =   form.find('select[name=training_direction_id]'),
        senlerSubscriptionId =  form.find('select[name=senler_subscription_id]');

    data.id =                       id.val();
    data.area_id =                  areaId.val();
    data.vk_group_id =              vkGroupId.val();
    data.lid_status_id =            lidStatuses.val();
    data.other_status =             otherStatus.val();
    data.training_direction_id =    trainingDirectionId.val();
    data.senler_subscription_id =   senlerSubscriptionId.val();

    Senler.saveSetting(data, callback);
}


/**
 * Колбек функция при сохранении настроек интеграции сенлера
 *
 * @param response
 */
function saveSenlerSettingCallback(response) {
    if (!checkResponseStatus(response)) {
        loaderOff();
    } else {
        window.location.reload();
    }
}


/**
 * Колбек функция при удалении настроек интеграции сенлера
 *
 * @param response
 */
function deleteSenlerSettingCallback(response) {
    if (!checkResponseStatus(response)) {
        loaderOff();
    } else {
        window.location.reload();
    }
}