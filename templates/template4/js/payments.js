var root = $("#rootdir").val();

$(function(){
    $("body")
        /**
         * Форма открытия/скрытия блока (таблицы) с существующими тарифами
         */
        .on("click", ".tarifs_show", function(e){
            e.preventDefault();
            var TarifsBlock = $(".tarifs");
            if (TarifsBlock.css("display") === "block") {
                TarifsBlock.hide("slow");
            } else {
                TarifsBlock.show("slow");
            }
        })

        .on("click", ".finances_payment_rate_config", function(e){
            e.preventDefault();
            var teacherRateConfigBlock = $(".teacher_rate_config_block");
            if (teacherRateConfigBlock.css("display") == "none") {
                teacherRateConfigBlock.show("slow");
            } else {
                teacherRateConfigBlock.hide("slow");
            }
        })

        /**
         * Удаление тарифа
         */
        .on("click", ".tariff_delete", function(e){
            e.preventDefault();
            let tariffId = $(this).data("model_id");
            Tarif.remove(tariffId, function(response) {
                showResponseNotification(response);
                refreshPayments();
            });
        })

        /**
         * Открытие всплывающего окна создания/редактирования nfhbaf
         */
        .on("click", ".tariff_edit", function(e){
            e.preventDefault();
            var tariffId = $(this).data("tariff_id");
            editTariffPopup(tariffId);
        })

        /**
         * Сохранения формы редактирования тарифа
         */
        .on("click", ".popop_tariff_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", function(response) {
                refreshPayments();
                swal({
                    type: 'success',
                    title: 'Данные тарифа успешно сохранены'
                });
            });
        });
});

/**
 * Открытие всплывающего окна создания / редактирования тарифа
 *
 * @param tariffId
 */
function editTariffPopup(tariffId) {
    $.ajax({
        type: 'GET',
        url: 'finances',
        data: {
            action: 'edit_tariff_popup',
            tariffId: tariffId
        },
        success: function(response) {
            showPopup(response);
        }
    });
}

/**
 * Обновление содержимого раздела "Финансы"
 */
function refreshPayments() {
    var dateFrom = $('input[name=date_from]').val();
    var dateTo = $('input[name=date_to]').val();
    var areaId = $('select[name=area_id]').val();

    $.ajax({
        type: 'GET',
        url: root + '/finances',
        data: {
            action: 'show',
            date_from: dateFrom,
            date_to: dateTo,
            area_id: areaId
        },
        success: function(response) {
            var isTarifsTableDisplay = $('.tarifs').css('display');
            $('.finances').html(response);
            $('.tarifs').css('display', isTarifsTableDisplay);
            loaderOff();
        }
    });
}

function refreshPaymentsTable(userId, func) {
    $.ajax({
        type: 'GET',
        url: root + '/balance',
        // async: false,
        data: {
            action: 'refreshTablePayments',
            user_id: userId,
            userid: userId
        },
        success: function(response) {
            $('.users').html(response);
            $('#sortingTable').tablesorter();
            func();
        }
    });
}

function getTarifPopup(id, type) {
    $.ajax({
        type: 'GET',
        url: root + '/balance',
        data: {
            action: 'getTarifPopup',
            type: type,
            userid: id
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}

/*-----------------------------------------------*/
/*---------------Новые обработчики---------------*/
/*-----------------------------------------------*/

/**
 * Формирование всплывающего окна редактирования платежа
 *
 * @param paymentId
 * @param afterSaveAction
 */
function makePaymentPopup(paymentId, afterSaveAction) {
    loaderOn();
    Payment.getPayment(paymentId, function(payment) {
        Payment.getCustomTypesList(function(types) {
            Schedule.clearCache();
            Schedule.getAreasList({}, function(areas) {
                var popupData = '<div class="popup-row-block">' +
                    '<div class="column"><span>Сумма</span><span style="color:red">*</span></div>' +
                    '<div class="column"><input type="number" name="value" class="form-control" value="'+payment.value+'"></div>' +
                    '<div class="column"><span>Дата</span><span style="color:red">*</span></div>' +
                    '<div class="column"><input type="date" name="datetime" class="form-control" value="'+payment.datetime+'"></div>';

                //Воможность редактирования типа доступно только для кастомных плтежей
                if (types.forEach(element => (Object.values(element)).includes((payment.typeId).toString()))|| payment.typeId == 0) {
                    popupData += '<div class="column"><span>Тип</span></div>';
                    popupData += '<div class="column"><select class="form-control" name="type" id="type">';
                    $.each(types, function(key, type) {
                        var isMatch = '';
                        if (payment.typeId == type.id) {
                            isMatch = 'checked';
                        }
                        popupData += '<option '+isMatch+' value="'+type.id+'">'+type.title+'</option>';
                    });
                    popupData += '</select></div>';
                } else {
                    popupData += '<input type="hidden" id="type" value="'+payment.typeId+'" />';
                }

                popupData +=
                    '<div class="column"><span>Филиал</span></div>' +
                    '<div class="column"><select name="areaId" id="areaId" class="form-control">' +
                        '<option value="0"> ... </option>';
                $.each(areas, function(key, area) {
                    var isMatch = '';
                    if (payment.areaId == area.id) {
                        isMatch = 'selected';
                    }
                    popupData += '<option value="'+area.id+'" '+isMatch+'>'+area.title+'</option>';
                });
                popupData += '</select></div>';

                popupData +=
                    '<div class="column"><span>Примечание</span><span style="color:red">*</span></div>' +
                    '<div class="column"><textarea name="description" class="form-control">'+payment.description+'</textarea></div>';

                popupData += '</div>';
                popupData += '<div class="row"><button class="btn btn-default"' +
                    'onclick="Payment.save(' +
                        payment.id+', ' +
                        payment.userId + ', ' +
                        '$(\'.popup-row-block\').find(\'input[name=value]\').val()' + ', ' +
                        '$(\'.popup-row-block\').find(\'#type\').val()' + ', ' +
                        '$(\'.popup-row-block\').find(\'input[name=datetime]\').val()' + ', ' +
                        '$(\'.popup-row-block\').find(\'#areaId\').val()' + ', ' +
                        '$(\'.popup-row-block\').find(\'textarea[name=description]\').val()' + ', ' +
                        '\'\', ' +
                        afterSaveAction +
                    '); closePopup();"' +
                    '>Сохранить</button></div>';
                showPopup(popupData);
                loaderOff();
            });
        });
    });
}

/**
 * Колбэк функция для сохранения платежа и раздела "Финансы"
 * Пока что тут стоит "функция-затычка" для бновления контента страницы, со временем надо бы доработать
 * ВНИМАНИЕ!!! в коде данной функции СТРОГО ЗАПРЕЩЕНО использовать двойные кавычки
 *
 * @param payment
 */
function savePaymentCallback(payment) {
    showFinancesHistory($('input[name=date_from]').val(), $('input[name=date_to]').val(), $('select[name=area_id]').val());
}

/**
 * Колбэк функция для удаления платежа и раздела "Финансы"
 * Пока что тут стоит "функция-затычка" для бновления контента страницы, со временем надо бы доработать
 *
 * @param payment
 */
function removePaymentCallback(payment) {
    showFinancesHistory($('input[name=date_from]').val(), $('input[name=date_to]').val(), $('select[name=area_id]').val());
}

/**
 * Функция создания всплывающего окна для начисления платежа клиенту
 *
 * @param paymentId
 * @param userId
 * @param saveCallback
 */
function makeClientPaymentPopup(paymentId, userId, saveCallback) {
    Payment.getPayment(paymentId, function(payment){
        if (userId == undefined) {
            userId = payment.user;
        }

        if (payment.typeId == '0') {
            payment.typeId = 1;
        } else {
            payment.typeId = Number(payment.typeId);
        }

        var popupData = "<div class='popup-row-block'><div class=\"column\">\n" +
            "                <span>Сумма</span><span style=\"color:red\" >*</span>\n" +
            "            </div>\n" +
            "            <div class=\"column\">\n" +
            "                <input type=\"number\" id=\"payment_value\" name=\"value\" class=\"form-control\" value=\""+payment.value+"\" />\n" +
            "            </div>\n" +
            "\n" +
            "            <div class=\"column\">\n" +
            "                <span>Примечание (общее)</span><span style=\"color:red\" >*</span>\n" +
            "            </div>\n" +
            "            <div class=\"column\">\n" +
            "                <textarea class=\"form-control\" id=\"payment_description\" name=\"description\">" +
                            payment.description +
                            "</textarea>\n" +
            "            </div>\n" +
            "\n" +
            "            <div class=\"column\">\n" +
            "                <span>Примечание (для админа)</span><span style=\"color:red\" >*</span>\n" +
            "            </div>\n" +
            "            <div class=\"column\">\n" +
            "                <textarea class=\"form-control\" id=\"payment_comment\" name=\"property_26\">";
                                if (payment.comments[0] != undefined) {
                                    popupData += payment.comments[0].text;
                                }
            popupData +=    "</textarea>\n" +
            "            </div>\n";

            var disabledType = '';
            if (payment.id > 0) {
                disabledType = 'disabled';
            }

            popupData +=
            "            <div class=\"column\">\n" +
            "                <span>Тип операции</span>\n" +
            "            </div>\n" +
            "            <div class=\"column\">\n" +
            "                <p style=\"margin-top: 5px\">\n" +
            "                    <input type=\"radio\" "+disabledType+" name=\"type\" id=\"type1\" value=\"1\" style=\"height: auto\"";
                                if (payment.typeId == 1) {
                                    popupData += " checked ";
                                }
            popupData += "       />\n" +
            "                    <label for=\"type1\">Зачисление</label>\n" +
            "                </p>\n" +
            "                <p style=\"margin-top: 5px\">\n" +
            "                    <input type=\"radio\" "+disabledType+" name=\"type\" id=\"type2\" value=\"2\" style=\"height: auto\"";
                                if (payment.typeId == 2) {
                                    popupData += " checked ";
                                }
            popupData += "       />\n" +
            "                    <label for=\"type2\">Списание</label>\n" +
            "                </p>\n" +
            "                <p style=\"margin-top: 5px\">\n" +
            "                    <input type=\"radio\" "+disabledType+" name=\"type\" id=\"type21\" value=\"21\" style=\"height: auto\"";
                                if (payment.typeId == 21) {
                                    popupData += " checked ";
                                }
            popupData += "       />\n" +
            "                    <label for=\"type21\">Бонус</label>\n" +
            "                </p>\n" +
            "            </div>";
            popupData += "<button class=\"btn btn-default\" " +
                "onclick=\"Payment.save(" +
                    paymentId + ", " +
                    userId + ", " +
                    "$('#payment_value').val(), " +
                    "$('input[type=radio]:checked').val(), " +
                    "'', " +
                    "0, " +
                    "$('#payment_description').val(), " +
                    "$('#payment_comment').val(), " +
                    saveCallback +
                ")\"" +
            ">Сохранить</button></div>";

        showPopup(popupData);
    });
}

/**
 * Коллбэк функция для изменения баланса клиента в общем списке
 * ВНИМАНИЕ!!! в коде данной функции СТРОГО ЗАПРЕЩЕНО использовать двойные кавычки
 *
 * @param payment
 */
function saveClientPaymentCallback(payment) {
    if (payment.error != undefined) {
        notificationError(payment.error.message);
        return false;
    }

    var balanceSpan = $('#user_' + payment.userId).find('.add__12');
    balanceSpan.text(payment.userBalance);
    closePopup();
}

/**
 * Коллбэк функция для создания платежа из личного кабинета ученика
 * ВНИМАНИЕ!!! в коде данной функции СТРОГО ЗАПРЕЩЕНО использовать двойные кавычки
 *
 * @param payment
 */
function saveBalancePaymentCallback(payment) {
    if (!checkResponseStatus(payment)) {
        return false;
    }

    var balanceSpan = $('#balance');
    balanceSpan.text(payment.userBalance);
    if (payment.typeId == '1' || payment.typeId == '15') {
        var trClass = 'positive';
    } else {
        var trClass = 'negative';
    }

    var paymentsTable = $('.user-payments').find('table');
    var paymentTr = $('#client_payment_' + payment.id);

    if (paymentTr.length == 0) {    //Если это новый платеж
        paymentTr = '<tr class=\''+trClass+'\' id=\'client_payment_'+payment.id+'\'>' +
            '<td class=\'date\'>'+payment.refactoredDatetime+'</td>' +
            '<td class=\'value\'>'+payment.value+'</td>' +
            '<td class=\'status\'><p class=\'text-success\'>Выполнен</p></td>' +
            '<td>' +
            '<p class=\'description\'>'+payment.description+'</p>' +
            '<span class=\'comments\'>';
        if (payment.comments[0] != undefined) {
            paymentTr += '<p class=\'comment_'+payment.comments[0].id+'\'>'+payment.comments[0].text+'</p>';
        }
        paymentTr += '</span>' +
            '</td>' +
            '<td class=\'center\'>' +
            '<a class=\'action comment\' title=\'Добавить комментарий\' ' +
                'onclick=\'makePaymentCommentPopup('+payment.id+', savePaymentCommentClient)\'' +
            '></a>';
            if (payment.accessEdit == true) {
                paymentTr += '<a class=\'action edit\' title=\'Редактирование платежа\'' +
                    'onclick=\'makeClientPaymentPopup('+payment.id+', '+payment.userId+', saveBalancePaymentCallback)\'></a>';
            }
            if (payment.accessDelete) {
                paymentTr += '<a class=\'action delete\' title=\'Удаление платежа\' ' +
                    'onclick=\'Payment.remove('+payment.id+', removeBalancePaymentCallback)\'></a>';
            }
        paymentTr += '</td></tr>';
        paymentsTable.prepend(paymentTr);
    } else {    //Если редактируется уже сущствующий платеж
        paymentTr.find('.date').text(payment.refactoredDatetime);
        paymentTr.find('.value').text(payment.value);
        paymentTr.find('.description').text(payment.description);
        var comments = paymentTr.find('.comments');
        comments.empty();
        if (payment.comments[0] != undefined) {
            $.each(payment.comments, function(key, comment){
                comments.append('<p class=\'comment_'+comment.id+'\'>'+comment.text+'</p>');
            });
        }
    }

    closePopup();
}

/**
 * Колбэк функция для удаления платежа клиента
 *
 * @param payment
 */
function removeBalancePaymentCallback(payment) {
    $('#client_payment_' + payment.id).remove();
    var clientBalance = $('#balance');
    var clientBalanceVal = Number(clientBalance.text());
    if (payment.type == 1) {
        clientBalance.text(clientBalanceVal - payment.value);
    } else {
        clientBalance.text(clientBalanceVal + Number(payment.value));
    }
}

/**
 * Формирование всплывающего окна для добавления примечания к платежу
 *
 * @param paymentId
 * @param callBack
 */
function makePaymentCommentPopup(paymentId, callBack) {
    var popupData = "<div class='popup-row-block'>" +
        "<textarea class=\"form-control\" id='payment_comment_popup'></textarea>" +
        "<button class=\"btn btn-default\" " +
            "onclick=\"Payment.appendComment("+paymentId+", $('#payment_comment_popup').val(), "+callBack+")\"" +
        ">Сохранить</button></div>" +
        "</div>";
    showPopup(popupData);
}

/**
 * Коллэк функция для сохранения комментария к платежу из личного каинета клиента
 * ВНИМАНИЕ!!! в коде данной функции СТРОГО ЗАПРЕЩЕНО использовать двойные кавычки
 *
 * @param response
 */
function savePaymentCommentClient(response) {
    $('#client_payment_' + response.payment.id)
        .find('.comments')
        .prepend('<p class=\'comment_'+response.comment.id+'\'>'+response.comment.text+'</p>');
    closePopup();
}

function saveTeacherPaymentCallback(payment) {
    if (payment.error != undefined) {
        notificationError(payment.error.message);
        return false;
    }
    refreshSchedule();
    return true;
}

function removeTeacherPaymentCallback(payment) {
    if (payment.error != undefined) {
        notificationError(payment.error.message);
        return false;
    }
    refreshSchedule();
    return true;
}

function checkPaymentStatusCallback(response) {
    checkResponseStatus(response);
    setTimeout(function () {
        window.location.reload();
    }, 1500);
}