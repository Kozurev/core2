"use strict";
var root = $('#rootdir').val();

$(function () {
    $(document)
        .on('click', '.show_lid_status', function(e) {
            e.preventDefault();
            var statusesTable = $('.lid_statuses_table');

            if(statusesTable.css('display') == 'block') {
                statusesTable.hide();
            } else {
                statusesTable.show();
            }
        })
        .on('click', '.edit_lid_status', function(e) {
            e.preventDefault();
            var statusId = $(this).data('id');
            getLidStatusPopup(statusId);
        })
        .on('click', '.lid_status_submit', function(e) {
            e.preventDefault();

            var
                Form =      $('#createData'),
                id =        Form.find('input[name=id]').val(),
                title =     Form.find('input[name=title]').val(),
                itemClass = Form.find('select[name=item_class]').val();

            saveLidStatus(id, title, itemClass, function(response) {
                closePopup();

                var statusSelects = $('.lid_status');

                if(id == '') {
                    var newTr = '<tr>' +
                        '<td>' + response.title + '</td>' +
                        '<td>' + response.colorName + '</td>' +
                        '<td>' +
                        '   <input type="radio" name="lid_status_consult" id="lid_status_consult_'+response.id+'" value="'+response.id+'">' +
                        '   <label for="lid_status_consult_'+response.id+'"></label></td>' +
                        '<td>' +
                        '   <input type="radio" name="lid_status_consult_attended" id="lid_status_consult_attended_'+response.id+'" value="'+response.id+'">' +
                        '   <label for="lid_status_consult_attended_'+response.id+'"></label>' +
                        '</td>' +
                        '<td>' +
                        '   <input type="radio" name="lid_status_consult_absent" id="lid_status_consult_absent_'+response.id+'" value="'+response.id+'">' +
                        '   <label for="lid_status_consult_absent_'+response.id+'"></label>' +
                        '</td>' +
                        '<td class="right">' +
                            '<a class="action edit edit_lid_status" data-id="' + response.id + '"></a>' +
                            '<a class="action delete delete_lid_status" data-id="' + response.id + '"></a>' +
                        '</td>' +
                        '</tr>';

                    var
                        table = $('#table-lid-statuses'),
                        lastTr = table.find('tr')[table.find('tr').length - 1],
                        lastTrClone = $(lastTr).clone();

                    $(lastTr).remove();
                    table.append(newTr);
                    table.append(lastTrClone);

                    $.each(statusSelects, function(key, select) {
                        $(select).append('<option value="' + response.id + '">' + response.title + '</option>');
                    });
                } else {
                    var
                        tr = $('.lid_statuses_table').find('.edit[data-id='+id+']').parent().parent(),
                        tdTitle = tr.find('td')[0],
                        tdColor = tr.find('td')[1];

                    $(tdTitle).text(response.title);
                    $(tdColor).text(response.colorName);

                    $.each(statusSelects, function(key, select) {
                        $(select).find('option[value='+response.id+']').text(response.title);
                    });

                    if(response.oldItemClass) {
                        var editingStatusCards = $('.' + response.oldItemClass);
                        $.each(editingStatusCards, function(key, card) {
                            $(card).removeClass(response.oldItemClass);
                            $(card).addClass(response.itemClass);
                        });
                    }
                }
            });
        })
        .on('click', '.delete_lid_status', function(e) {
            e.preventDefault();

            var id = $(this).data('id');

            deleteLidStatus(id, function(response) {
                $('.lid_statuses_table').find('.edit[data-id='+response.id+']').parent().parent().remove();

                var statusSelects = $('.lid_status');
                $.each(statusSelects, function(key, select) {
                    $(select).find('option[value='+response.id+']').remove();
                });

                var deletedStatusItems = $('.' + response.itemClass);
                $.each(deletedStatusItems, function(key, card) {
                    $(card).removeClass(response.itemClass);
                });
            });
        })
        .on('change', '#source_select', function(e) {
            var sourceInput = $('#source_input');

            if($(this).val() == 0) {
                sourceInput.show();
            } else {
                sourceInput.val('');
                sourceInput.hide();
            }
        })
        .on('change', '#editLidForm input[name=number]', function(e) {
            let $duplicateText = $('#duplicatedLidText');
            $duplicateText.empty();
            if ($(this).val().length === 12) {
                Lids.getList({
                    filter: {
                        number: $(this).val()
                    }
                }, function(lids) {
                    if (lids.length !== 0) {
                        $duplicateText.text('Лид(ы) с таким номером телефона уже существуют: ');
                        $(lids).each(function(key, lid) {
                            $duplicateText.append('№<a class="info-by-id" data-model="Lid" data-id="'+lid.id+'">' + lid.id + '</a><span> </span>');
                        });
                    }
                });
            }
        })
        .on('click', '.lid_group_setting', function(e) {
            e.preventDefault();
            let lidId = $(this).data('id'),
                $modal = $('#lidsGroupsModal');
            $modal.find('input[name=object_id]').val(lidId);
            // $modal.find('select').selectpicker('deselectAll');
            // $modal.find('select').selectpicker('refresh');
            $modal.modal();
        });

    if ($('#lids_groups_ajax_form').length !== 0) {
        initAjaxForm('#lids_groups_ajax_form', function(response) {
            $('#lidsGroupsModal').modal('hide');
            ajaxFormSuccessCallbackDefault(response);
        });
    }

        $('#table-lid-statuses').on('change', 'input[type=radio]', function() {
            var
                propName =      $(this).attr('name'),
                propVal =       $(this).val(),
                directorId =    $('#directorid').val(),
                statusName =    $(this).parent().parent().find('td')[0];
            statusName = $(statusName).text();

            savePropertyValue(propName, propVal, 'User', directorId, function() {
                var msg = 'Статусом лида после ';

                switch(propName)
                {
                    case 'lid_status_consult':
                        msg += 'создания консультации';
                        break;
                    case 'lid_status_consult_attended':
                        msg += 'посещения консультации';
                        break;
                    case 'lid_status_consult_absent':
                        msg += 'пропуска консультации';
                        break;
                    case 'lid_status_client' :
                        msg += 'записи';
                        break;
                    default: msg = 'Неизвестной настройке';
                }

                msg += ' установлен: \''+ statusName +'\'';
                notificationSuccess(msg);
            });
        });
});


/**
 * Перезагрузка блока с классом lids
 */
function refreshLidTable(page) {
    loaderOn();
    let filtersForm = $('#filter_lids');
    let data = filtersForm.serialize();
    data += '&action=refreshLidTable';

    if (page !== undefined && page > 0) {
        data += '&page=' + page;
    }

    $.ajax({
        type: 'GET',
        url: '',
        data: data,
        success: function (response) {
            $('.lids').html(response);
            loaderOff();
        }
    });
}



/**
 * Открытие всплывающего окна создания/редактирования статуса лида
 *
 * @param id
 */
function getLidStatusPopup(id) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'getLidStatusPopup',
            id: id
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        },
        error: function(response) {
            closePopup();
            notificationError('Ошибка: редактируемый статус не существует либо принадлежит другой организации');
            loaderOff();
        }
    });
}


/**
 * Открытие всплывающего окна событий,связанных с лидом
 *
 * @param id
 */
function getLidStatisticPopup(id) {
    loaderOn();
    $.ajax({
        type: 'GET',
        url: root + '/lids',
        data: {
            action: 'getLidStatisticPopup',
            id: id
        },
        success: function(response) {
            showPopup(response);
            loaderOff();
        },
        error: function(response) {
            closePopup();
            notificationError('Ошибка: Что то пошло не так');
            loaderOff();
        }
    });
}

/**
 * Создание/редактирование данных статуса лида
 *
 * @param id
 * @param title
 * @param itemClass
 * @param callback
 */
function saveLidStatus(id, title, itemClass, callback) {
    loaderOn();

    $.ajax({
        type: 'GET',
        url: root + '/lids',
        dataType: 'json',
        data: {
            action: 'saveLidStatus',
            id: id,
            title: title,
            item_class: itemClass
        },
        success: function(response) {
            callback(response);
            loaderOff();
        },
        error: function(response) {
            notificationError('При сохранении статуса лида произошла ошибка');
        }
    });
}


/**
 * Удаление статуса лида
 *
 * @param id
 * @param callback
 */
function deleteLidStatus(id, callback) {
    loaderOn();

    $.ajax({
        type: 'GET',
        url: root + '/lids',
        dataType: 'json',
        data: {
            action: 'deleteLidStatus',
            id: id
        },
        success: function(response) {
            callback(response);
            loaderOff();
        },
        error: function(response) {
            closePopup();
            notificationError('Ошибка: удаляемый статус не существует либо принадлежит другой организации');
            loaderOff();
        }
    });
}



/*---------------------------------------------------------------------------------------------*/
/*--------------------------------------Новые обработчики--------------------------------------*/
/*---------------------------------------------------------------------------------------------*/
$(function(){
    $('body')
        //Обработчик события поиска лидов по заданным параметрам
        .on('click', '.lids_search', function(e) {
            e.preventDefault();
            refreshLidTable();
        })
        //Обновление данных страницы аналитики лидов
        .on('click', '.lids_statistic_show', function(e){
            e.preventDefault();
            loaderOn();
            let formData = new FormData($('#filter_lids_statistic').get(0));
            formData.append('markerId', $('#lid_statistic_markerId').val());
            formData.append('sourceId', $('#lid_statistic_sourceId').val());
            formData.append('teacherId', $('#lids_statistic_teacherId').val());
            formData.append('areaId', $('#lids_statistic_areaId').val());
            //formData.append('action', 'refresh');
            $.ajax({
                type: 'POST',
                url: root + '/lids/statistic?action=refresh',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('.lids').html(response);
                    loaderOff();
                },
                error: function () {
                    notificationError('Произошла ошибка');
                    loaderOff();
                }
            });
        })
        .on('change', '#lid_statistic_markerId, #lid_statistic_sourceId, #lids_statistic_teacherId', function(){
            $('.lids_statistic_show').trigger('click');
        })
        .on('click', '.lids_consult_show', function(e){
            e.preventDefault();
            loaderOn();
            let formData = $('#filter_lids').serialize();
            $.ajax({
                type: 'GET',
                url: root + '/lids/consults?action=refresh',
                data: formData,
                success: function (response) {
                    $('.lids').html(response);
                    loaderOff();
                },
                error: function () {
                    notificationError('Произошла ошибка');
                    loaderOff();
                }
            });
        });
});


/**
 * Функция для экспорта лидов в excel из раздела "Консультации"
 *
 * @param filterForm
 */
function lidsExport(filterForm) {
    var link = root + '/lids/consults?action=export';
    if (filterForm === undefined) {
        window.location.href = link;
    } else  {
        window.location.href = link + '&' + filterForm.serialize();
    }
}

/**
 * Функция экпорта лидов в excel из раздела "Экспорт"
 *
 * @param filterForm
 */
function lidsExportWithFilter(filterForm) {
    var link = root + '/lids/export?action=export';
    if (filterForm === undefined) {
        window.location.href = link;
    } else  {
        window.location.href = link + '&' + filterForm.serialize();
    }
}


/**
 * Формирование всплывающего окна создания/редактирования лида
 *
 * @param lidId
 */
function makeLidPopup(lidId) {
    loaderOn();

    //Поиск информации о лиде
    Lids.getLid(lidId, function(lid){
        let popupData =
            '<div class="popup-row-block" id="editLidForm">' +
            '<div class="column"><span>Фамилия</span></div>' +
            '<div class="column"><input class="form-control" type="text" name="surname" value="'+lid.surname+'"></div>' +
            '<hr>' +
            '<div class="column"><span>Имя</span></div>' +
            '<div class="column"><input class="form-control" type="text" name="name" value="'+lid.name+'"></div>' +
            '<hr>' +
            '<div class="column"><span>Номер телефона</span></div>' +
            '<div class="column"><input class="form-control masked-phone" type="text" name="number" value="'+lid.number+'"><p class="text-danger" id="duplicatedLidText"></p></div>' +
            '<hr>' +
            '<div class="column"><span>Ссылка ВК</span></div>' +
            '<div class="column"><input class="form-control" type="text" name="vk" value="'+lid.vk+'"></div>' +
            '<hr>' +
            '<div class="column"><span>Дата контроля</span></div>' +
            '<div class="column"><input class="form-control" type="date" name="control_date" value="'+lid.control_date+'"></div>' +
            '<hr>' +
            '<div class="column"><span>Статус</span></div>' +
            '<div class="column">' +
                '<select class="form-control" name="status_id" id="status_id">' +
                '</select>' +
            '</div>' +
            '<hr>' +
            '<div class="column"><span>Филиал</span></div>' +
            '<div class="column">' +
                '<select class="form-control" name="area_id" id="area_id">' +
                    '<option value="0"> ... </option>' +
                '</select>' +
            '</div>' +
            '<hr>' +
            '<div class="column"><span>Маркер</span></div>' +
            '<div class="column">' +
                '<select class="form-control" name="property_54" id="property_54">' +
                    '<option value="0"> ... </option>' +
                '</select>' +
            '</div>' +
            '<hr>' +
            '<div class="column"><span>Источник</span></div>' +
            '<div class="column">' +
                '<select class="form-control" name="property_50" id="source_select">' +
                    '<option value="0">Другое</option>' +
                '</select>' +
                '<input class="form-control" type="text" value="'+lid.source+'" id="source_input" name="source" placeholder="Источник">' +
            '</div>' +
            '<hr>' +
            '<div class="column"><span>Направление подготовки</span></div>' +
            '<div class="column">' +
                '<select class="form-control" name="property_20" id="instrument_select">' +
                    '<option value="0">Не определен</option>' +
                '</select>' +
            '</div>' +
            '<hr>' +
            '<div class="column"><span>Приоритет</span></div>' +
            '<div class="column">' +
                '<select class="form-control" name="priorityId" id="priorityId">' +
            '</select>' +
            '</div>' +
            '<hr/>' +
            '<div class="column">' +
            '    <span>Смс оповещения</span>' +
            '</div>' +
            '<div class="column">' +
            '    <input class="checkbox" id="sms_notification" type="checkbox" '+ (lid.sms_notification == "1" ? ' checked ' : ' ') +' name="sms_notification">' +
            '    <label for="sms_notification" class="checkbox-label">\n' +
            '        <span class="off">Отключены</span>\n' +
            '        <span class="on">Включены</span>\n' +
            '    </label>\n' +
            '</div>' +
            '<hr/>';

        if (lidId == 0) {
            popupData +=
                '<hr>' +
                '<div class="column"><span>Комментарий</span></div>' +
                '<div class="column"><textarea class="form-control" name="comment"></textarea></div>';
        }

        popupData +=
            '<input type="hidden" value="'+lid.id+'" name="id" id="id" />' +
            '<button class="btn btn-default" onclick="saveLidFrom($(\'#editLidForm\'), saveLidCallback)">Сохранить</button>' +
            '</div>';

        prependPopup(popupData);

        $(".masked-phone").mask("+79999999999");

        let isSelected;

        Schedule.clearCache();
        Lids.clearCache();
        PropertyList.clearCache(50);
        PropertyList.clearCache(54);

        //Подгрузка списка источников
        PropertyList.getList(54, function(markers){
            if (typeof markers.error != 'undefined') {
                notificationError(markers.error.message);
                return false;
            } else {
                let markersList = $('#property_54');
                $.each(markers, function(key, marker){
                    isSelected = marker.id == lid.property_54[0].value_id ? 'selected' : '';
                    markersList.append('<option value="'+marker.id+'" '+isSelected+'>'+marker.value+'</option>');
                });

                //Подгрузка списка маркеров
                PropertyList.getList(50, function(sources){
                    if (typeof sources.error != 'undefined') {
                        notificationError(sources.error.message);
                        return false;
                    } else {
                        let sourceList = $('#source_select');
                        $.each(sources, function(key, source){
                            isSelected = source.id == lid.property_50[0].value_id ? 'selected' : '';
                            sourceList.append('<option value="'+source.id+'" '+isSelected+'>'+source.value+'</option>');
                            if (isSelected != '') {
                                $('#source_input').css('display', 'none');
                            }
                        });

                        //Подгрузка статусов
                        Lids.getStatusList(function(statuses){
                            let statusList = $('#status_id');
                            $.each(statuses, function(key, status){
                                isSelected = status.id == lid.status_id ? 'selected' : '';
                                statusList.append('<option value="'+status.id+'" '+isSelected+'>'+status.title+'</option>');
                            });

                            //Подгрузка филиалов
                            Schedule.getAreasList({isRelated:true}, function(areas){
                                let areasList = $('#area_id');
                                $.each(areas, function(key, area){
                                    isSelected = area.id == lid.area_id ? 'selected' : '';
                                    areasList.append('<option value="'+area.id+'" '+isSelected+'>'+area.title+'</option>');
                                });

                                Lids.getPriorityList(function(priorities){
                                    let priorityList = $('#priorityId');
                                    $.each(priorities, function(key, priority){
                                        isSelected = priority.id == lid.priority_id ? 'selected' : '';
                                        priorityList.append('<option value="'+priority.id+'" '+isSelected+'>'+priority.title+'</option>');
                                    });

                                    PropertyList.getList(20, function(instruments) {
                                        let instrumentsList = $('#instrument_select');
                                        $.each(instruments, function(key, instrument){
                                            isSelected = instrument.id == lid.property_20[0].value_id ? 'selected' : '';
                                            instrumentsList.append('<option value="'+instrument.id+'" '+isSelected+'>'+instrument.value+'</option>');
                                        });

                                        loaderOff();
                                        showPopup();
                                    });
                                });
                            });
                        });
                    }
                });
            }
        });
    });
}


/**
 * Рендеринг новой карточки лида и добавление её в начало родительского блока
 *
 * @param lid
 * @param block
 */
function prependLidCard(lid, block) {
    let isSelected, style;
    let card =
        '<div class="item lid_'+lid.id+'">' +
            '<div class="item-inner">' +
                '<div class="row">' +
                    '<div class="col-sm-3 col-xs-12">' +
                        '<h3 class="title">' +
                            '<span class="id">'+lid.id+' </span>' +
                            '<span class="surname">'+lid.surname+' </span>' +
                            '<span class="name">'+lid.name+' </span>' +
                        '</h3>';

                        style = lid.number == '' ? 'style="display:none"' : '';
                        card += '<p class="intro" '+style+'><span class="number">'+lid.number+'</span></p>';

                        style = lid.vk == '' ? 'style="display:none"' : '';
                        card += '<p class="intro" '+style+'><span>ВК: </span><span class="vk">'+lid.vk+'</span></p>';

                        card +=
                            '<a class="action edit" onclick="makeLidPopup('+lid.id+')" title="Редактировать лида"></a>' +
                            '<a class="action comment" title="Добавить комментарий" onclick="makeLidCommentPopup(0, '+lid.id+', saveLidCommentCallback)"></a>' +
                            '<a class="action add_user" title="Создать пользователя" onclick="makeClientFromLidPopup('+lid.id+')"></a>' +
                            '<a class="action calendar" onclick="getLidStatisticPopup('+lid.id+')" title="События лида"></a>';

                        card +=
                        '<input type="date" class="form-control date_inp lid_date" onchange="Lids.changeDate('+lid.id+', this.value)" value="'+lid.control_date+'">' +
                        '<select name="status" class="form-control lid_status" onchange="Lids.changeStatus('+lid.id+', this.value, changeLidStatusCallback)">' +
                            '<option value="0"> ... </option>' +
                        '</select>' +
                        '<select class="form-control lid-area" onchange="Lids.changeArea('+lid.id+', this.value)">' +
                            '<option value="0"> ... </option>' +
                        '</select>' +
                        '<select class="form-control lid_priority" onchange="Lids.changePriority('+lid.id+', this.value)">' +
                        '</select>';

                        style = empty(lid.property_54[0].value_id) ? 'style="display:none"' : '';
                        card += '<p class="intro" '+style+'><span>Маркер: </span><span class="marker">'+lid.property_54[0].value+'</span></p>';

                        style = empty(lid.source) && empty(lid.property_50[0].value_id) ? 'style="display:none"' : '';
                        let source = '';
                        if (!empty(lid.source) || !empty(lid.property_50[0].value_id)) {
                            if (lid.source != '') {
                                source = lid.source;
                            } else {
                                source = lid.property_50[0].value;
                            }
                        }
                        card += '<p class="intro" '+style+'><span>Источник: </span><span class="source">'+source+'</span></p>';
                    card += '</div>' +
                    '<div class="col-sm-9 col-xs-12 comments-column">' +
                        '<div class="comments">';
                            $.each(lid.comments, function(key, comment){
                                card += makeLidCommentBlock(comment);
                            });
    card +=
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

    block.prepend(card);
    let lidCard = block.find('.lid_' + lid.id);

    //Подгрузка статусов
    Lids.getStatusList(function (statuses) {
        let statusList = lidCard.find('.lid_status');
        $.each(statuses, function (key, status) {
            isSelected = status.id == lid.status_id ? 'selected' : '';
            if (isSelected != '') {
                lidCard.addClass(status.item_class);
            }
            statusList.append('<option value="' + status.id + '" ' + isSelected + '>' + status.title + '</option>');
        });

        //Подгрузка филиалов
        Schedule.getAreasList({isRelated: true}, function (areas) {
            let areasList = lidCard.find('.lid-area');
            $.each(areas, function (key, area) {
                isSelected = area.id == lid.area_id ? 'selected' : '';
                areasList.append('<option value="' + area.id + '" ' + isSelected + '>' + area.title + '</option>');
            });

            //Подгрузка приоритетов
            Lids.getPriorityList(function (priorities) {
                let priorityList = lidCard.find('.lid_priority');
                $.each(priorities, function (key, priority) {
                    isSelected = priority.id == lid.priority_id ? 'selected' : '';
                    priorityList.append('<option value="' + priority.id + '" ' + isSelected + '>' + priority.title + '</option>');
                });
            });
        });
    });
}


/**
 * ФОрмирование HTML блока комментария
 *
 * @param comment
 * @returns {string}
 */
function makeLidCommentBlock(comment) {
    let block =
        '<div class="block">' +
            '<div class="comment_header">' +
                '<div class="author">'+comment.author_fullname+'</div>' +
                '<div class="date">'+comment.refactoredDatetime+'</div>' +
            '</div>' +
            '<div class="comment_body">'
                +comment.text;
    if (typeof comment.files != 'undefined' && comment.files.length > 0) {
        block +=
                '<hr/>' +
                '<div class="comment_files">';
        $.each(comment.files, function(key, file){
            block += '<a target="_blank" href="'+file.link+'">'+file.real_name+'</a><br/>';
        });
        block += '</div>';
    }
    block +=
            '</div>' +
        '</div>';
    return block;
}


function makeLidCommentPopup(commentId, lidId, callback) {
    let popupData =
        '<div class="popup-row-block">' +
        '<div class="column"><span>Комментарий</span></div>' +
        '<div class="column"><textarea name="text" id="lidCommentText"></textarea></div>' +
        '<div class="column"><span>Доп. файлы</span></div>' +
        '<div class="column"><input type="file" name="lidCommentFile" id="lidCommentFile" /></div>' +
        '<button class="btn btn-default" ' +
        'onclick="Lids.saveComment('+commentId+', '+lidId+', $(\'#lidCommentText\').val(), '+callback+')">Сохранить</button>' +
        '</div>';
    showPopup(popupData);
}


function saveLidCommentCallback(comment) {
    if (comment.status == false) {
        notificationError(comment.message);
        loaderOff();
    } else {
        let lidCommentsBlock = $('.lid_' + comment.lid_id).find('.comments');
        let commentFile = $('#lidCommentFile');
        if (commentFile.get(0) === undefined || commentFile.get(0).files.length == 0) {
            lidCommentsBlock.prepend(makeLidCommentBlock(comment));
        } else {
            //Загрузка файла и прикрепление его к комментарию
            FileManager.upload(0, 0, commentFile, 'Comment', comment.id, function(response) {
                comment.files = [response.file];
                lidCommentsBlock.prepend(makeLidCommentBlock(comment));
            });
        }
    }

    closePopup();
}


/**
 * Сохранение данных лида с формы
 *
 * @param form
 * @param callback
 */
function saveLidFrom(form, callback) {
    loaderOn();
    let lidData = {};
    lidData.id = form.find('input[name=id]').val();
    lidData.surname = form.find('input[name=surname]').val();
    lidData.name = form.find('input[name=name]').val();
    lidData.number = form.find('input[name=number]').val();
    lidData.vk = form.find('input[name=vk]').val();
    lidData.controlDate = form.find('input[name=control_date]').val();
    lidData.statusId = form.find('select[name=status_id]').val();
    lidData.areaId = form.find('select[name=area_id]').val();
    lidData.source = form.find('input[name=source]').val();
    lidData.priorityId = form.find('select[name=priorityId]').val();
    lidData.property_20 = form.find('select[name=property_20]').val();
    lidData.property_50 = form.find('select[name=property_50]').val();
    lidData.property_54 = form.find('select[name=property_54]').val();
    lidData.sms_notification = form.find('input[name=sms_notification]').is(':checked') ? 1 : 0;
    let comment = form.find('textarea[name=comment]');
    if (comment.length > 0) {
        lidData.comment = comment.val();
    }
    Lids.save(lidData, callback);
}


/**
 * Колбек функция при сохранении лида
 *
 * @param lid
 */
function saveLidCallback(lid) {
    let lidsSection = $('.section-lids').find('.cards-wrapper'),
        lidCard = $('.lid_' + lid.id);

    if (lidCard.length == 0) {
        prependLidCard(lid, lidsSection);
    } else {
        lidCard.find('.surname').text(lid.surname + ' ');
        lidCard.find('.name').text(lid.name + ' ');

        let number = lidCard.find('.number');
        if (lid.number == '') {
            number.empty();
            number.parent().hide();
        } else {
            number.text(lid.number);
            number.parent().show();
        }

        let vk = lidCard.find('.vk');
        if (lid.vk == '') {
            vk.empty();
            vk.parent().hide();
        } else {
            vk.text(lid.vk);
            vk.parent().show();
        }

        let marker = lidCard.find('.marker');
        if (lid.property_54[0].value_id == 0) {
            marker.empty();
            marker.parent().hide();
        } else {
            marker.text(lid.property_54[0].value);
            marker.parent().show();
        }

        let source = lidCard.find('.source');
        if (lid.source == '' && lid.property_50[0].value_id == 0) {
            source.empty();
            source.parent().hide();
        } else {
            if (lid.source != '') {
                source.text(lid.source);
            } else {
                source.text(lid.property_50[0].value);
            }
            source.parent().show();
        }

        lidCard.find('.lid_date').val(lid.control_date);
        lidCard.find('.lid-area').val(lid.area_id);
        lidCard.find('.lid_priority').val(lid.priority_id);
        lidCard.find('.lid_status').val(lid.status_id);
        if (lid.status_id > 0) {
            lidCard.attr('class', 'item ' + lid.status.item_class + ' lid_' + lid.id);
        }
    }
    closePopup();
    loaderOff();
}


function changeLidStatusCallback(response) {
    if (response.status == false) {
        notificationError(response.message);
    } else {
        let lidCard = $('.lid_' + response.lid.id);
        lidCard.attr('class', 'item ' + response.status.item_class + ' lid_' + response.lid.id);
    }
}


function makeClientFromLidPopup(lidId) {
    loaderOn();
    makeClientPopup(0, function () {
        $('#lid_id').val(lidId);
        $('#get_lid_data').trigger('click');
        localStorage.setItem('clientFromLidId', lidId);
        $('.popup').find('.btn-default').attr('onclick', 'User.saveFrom(\'#createData\', makeClientFromLidCallback)');
        showPopup();
    });
}


function makeClientFromLidCallback(client) {
    if (client.error == undefined) {
        Lids.getPrioritySetting(Lids.STATUS_CLIENT, function(status){
            let lidId = localStorage.getItem('clientFromLidId');
            localStorage.removeItem('clientFromLidId');
            Lids.changeStatus(lidId, status.id, function(response){
                Lids.getLid(lidId, function(lid){
                    Lids.saveComment(0, lidId, 'Добавлен в клиенты', saveLidCommentCallback);
                });
                changeLidStatusCallback(response);
                loaderOff();
                closePopup();
            });
        });
    } else {
        notificationError(client.error.message);
        loaderOff();
    }
}


function changeClientsPage(page) {
    if (page > 0) {
        let form = $('#client-filter');
        form.append('<input type="hidden" name="page" value="'+page+'" />');
        applyClientFilter(form, function(response) {
            $('.users').html(response);
        });
    }
}