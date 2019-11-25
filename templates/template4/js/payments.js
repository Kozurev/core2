//var root = "/musadm";
var root = $("#rootdir").val();

$(function(){
    $("body")
        //Открытие формы добавления комментария к платежу
        // .on("click", ".payment_add_note", function(){
        //     var modelid = $(this).data("modelid");
        //     $.ajax({
        //         type: "GET",
        //         url: root + "/balance",
        //         data: {
        //             action: "add_note",
        //             model_id: modelid
        //         },
        //         success: function(responce){
        //             showPopup(responce);
        //         }
        //     });
        // })
        // //Отправка формы комментария платежа
        // .on("click", ".popop_payment_note_submit", function(e){
        //     e.preventDefault();
        //     loaderOn();
        //     var userid = $(this).data("userid");
        //     var note = $('#property_26').val();
        //     if (note == '') {
        //         closePopup();
        //         loaderOff();
        //         return;
        //     }
        //     saveData("Main", function(response){loaderOff();});
        //     refreshPaymentsTable(userid, loaderOff);
        // })
        //Открытие формы пополнения баланса
        // .on("click", ".btn_balance", function(e){
        //     e.preventDefault();
        //     var userid = $(this).data("userid");
        //     getPaymentPopup(userid, root + "/balance");
        // })
        //Отправка формы пополнения баланса
        // .on('click', '.popop_balance_payment_submit', function(e){
        //     e.preventDefault();
        //     loaderOn();
        //     var form = $('#createData');
        //     if($(form).valid() == false) {
        //         loaderOff();
        //         return;
        //     }
        //     var userid = $(this).data('userid');
        //     var value = $(form).find('input[name=value]').val();
        //     var description = $(form).find('textarea[name=description]').val();
        //     var description2 = $(form).find('textarea[name=property_26]').val();
        //     var type = $(form).find('input[name=type]:checked').val();
        //
        //     //Если это страница со списком клиентов
        //     if ($('#payment_from').val() == 'clients') {
        //         savePayment(userid, value, description, description2, type, 'balance', refreshUserTable);
        //     } else {
        //         savePayment(userid, value, description, description2, type, 'balance', function() {
        //             refreshPaymentsTable(userid, loaderOff);
        //         });
        //     }
        //     loaderOff();
        // })
        //Открытие формы покупки индивидуальных уроков
        // .on("click", ".btn_private_lessons", function(e){
        //     e.preventDefault();
        //     var userid = $(this).data("userid");
        //     getTarifPopup(userid, 1);
        // })
        // //Открытие формы покупки групповых уроков
        // .on("click", ".btn_group_lessons", function(e){
        //     e.preventDefault();
        //     var userid = $(this).data("userid");
        //     getTarifPopup(userid, 2);
        // })

        /**
         * Покупка тарифа
         */
        // .on('click', '.popop_buy_tarif_submit', function(e) {
        //     e.preventDefault();
        //     loaderOn();
        //     var tarifid = $('select[name=tarif_id]').val();
        //     var userid = $(this).data('userid');
        //     buyTarif(userid, tarifid);
        // })

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
        .on("click", ".tarif_delete", function(e){
            e.preventDefault();
            var tarifid = $(this).data("model_id");
            deleteItem("Payment_Tarif", tarifid, refreshPayments);
        })

        /**
         * Открытие всплывающего окна создания/редактирования nfhbaf
         */
        .on("click", ".tarif_edit", function(e){
            e.preventDefault();
            var tarifid = $(this).data("tarifid");
            editTarifPopup(tarifid);
        })

        /**
         * Сохранения формы редактирования тарифа
         */
        .on("click", ".popop_tarif_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", function(response){refreshPayments();});
        });

        /**
         * Открытие всплывающего окна создания/редактирования платежа
         */
        // .on('click', '.payment_edit', function(e) {
        //     e.preventDefault();
        //     var id = $(this).data('id');
        //     var afterSaveAction = $(this).data('after_save_action');
        //
        //     if (afterSaveAction == 'payment' && $(this).data('type') < 3) {
        //         editPaymentPopup(id, afterSaveAction);
        //     } else {
        //         editPaymentPopup(id, afterSaveAction);
        //     }
        // })

        // .on('click', '.payment_delete', function(e){
        //     e.preventDefault();
        //     loaderOn();
        //     var id = $(this).data('id');
        //     var afterSaveAction = $(this).data('after_save_action');
        //     deletePayment(id, function(response) {
        //         if (afterSaveAction == 'client') {
        //             refreshUserTable();
        //         }
        //         loaderOff();
        //     });
        // })

        /**
         * Сохранение формы редактирования платежа
         */
        // .on("click", ".popop_payment_submit", function(e){
        //     e.preventDefault();
        //     loaderOn();
        //
        //     var afterSaveAction = $("#createData").find("input[name=after_save_action]").val();
        //
        //     saveData("Main", function(response){
        //         /**
        //          * Сохранение изменений свойств платежа может происходить из разных разделов и требуют
        //          * различных действия для обновления контента страницы.
        //          * На данный момент информация о платеже редактируется из разделов клиента и страницы расписания преподавателя
        //          */
        //         switch (afterSaveAction)
        //         {
        //             case 'client':  //обновление контента страницы клиента
        //                 refreshUserTable();
        //                 break;
        //             case 'teacher': //обновление контента страницы преподавателя
        //                 refreshSchedule();
        //                 break;
        //             case 'payment': //обновление контента страницы финансов
        //                 refreshPayments();
        //                 break;
        //             default: loaderOff();
        //         }
        //     });
        // })
        //.on("click", ".teacher_payment_delete", function(e){
            // e.preventDefault();
            // loaderOn();
            // var id = $(this).data("id");
            // var paymentValue = Number( $(this).parent().parent().parent().find(".value").text() );
            //
            // var debt = $("#teacher-debt");
            // var alreadyPayed = $("#teacher-payed");
            //
            // var debtVal = Number(debt.text());
            // var alreadyPayedVal = Number(alreadyPayed.text());
            //
            // debt.text(debtVal + paymentValue);
            // alreadyPayed.text(alreadyPayedVal - paymentValue);
            //
            // $(this).parent().parent().parent().remove();
            //
            // deletePayment(id, function(response){
            //     loaderOff();
            // });
        //});
});


// /**
//  * Функция удаления платежа
//  *
//  * @date 21.01.2019 09:50
//  * @param paymentId
//  * @param func
//  */
// function deletePayment(paymentId, func) {
//     $.ajax({
//         type: 'GET',
//         url: root + '/balance',
//         data: {
//             action: 'payment_delete',
//             id: paymentId
//         },
//         success: function(response) {
//             func(response);
//         }
//     });
// }


// function editPaymentPopup(id, afterSaveAction) {
//     $.ajax({
//         type: 'GET',
//         url: root + '/finances',
//         data: {
//             action: 'edit_payment',
//             id: id,
//             afterSaveAction: afterSaveAction
//         },
//         success: function(response){
//             showPopup(response);
//         }
//     });
// }


/**
 * Открытие всплывающего окна создания / редактирования тарифа
 *
 * @param tarifId
 */
function editTarifPopup(tarifId) {
    $.ajax({
        type: 'GET',
        url: 'finances',
        data: {
            action: 'edit_tarif_popup',
            tarifid: tarifId
        },
        success: function(responce) {
            showPopup(responce);
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
        async: false,
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
            Schedule.getAreasList({}, function(areas) {
                var popupData = '<div class="popup-row-block">' +
                    '<div class="column"><span>Сумма</span><span style="color:red">*</span></div>' +
                    '<div class="column"><input type="number" name="value" class="form-control" value="'+payment.value+'"></div>' +
                    '<div class="column"><span>Дата</span><span style="color:red">*</span></div>' +
                    '<div class="column"><input type="date" name="datetime" class="form-control" value="'+payment.datetime+'"></div>';

                //Воможность редактирования типа доступно только для кастомных плтежей

                if (types.forEach(element => (Object.values(element)).includes((payment.typeId).toString()))) {
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
    var balanceVal = Number(balanceSpan.text());
    if (payment.typeId == '1') {
        balanceVal += payment.value;
    } else {
        balanceVal -= payment.value;
    }
    balanceSpan.text(balanceVal);
    closePopup();
}


/**
 * Коллбэк функция для создания платежа из личного кабинета ученика
 * ВНИМАНИЕ!!! в коде данной функции СТРОГО ЗАПРЕЩЕНО использовать двойные кавычки
 *
 * @param payment
 */
function saveBalancePaymentCallback(payment) {
    if (payment.error != undefined) {
        notificationError(payment.error.message);
        return false;
    }

    var balanceSpan = $('#balance');
    balanceSpan.text(payment.userBalance);
    if (payment.typeId == '1') {
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
            '></a>' +
            '<a class=\'action edit\' title=\'Редактирование платежа\'' +
                'onclick=\'makeClientPaymentPopup('+payment.id+', '+payment.user+', saveBalancePaymentCallback)\'' +
            '></a>' +
            '<a class=\'action delete\' title=\'Удаление платежа\' ' +
                'onclick=\'Payment.remove('+payment.id+', removeBalancePaymentCallback)\'' +
            '></a>' +
            '</td>' +
            '</tr>';
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
    console.log(payment.type == 1);
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