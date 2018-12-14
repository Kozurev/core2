//var root = "/musadm";
var root = $("#rootdir").val();

$(function(){
    $("body")
        //Открытие формы добавления комментария к платежу
        .on("click", ".payment_add_note", function(){
            var modelid = $(this).data("modelid");
            $.ajax({
                type: "GET",
                url: root + "/balance",
                data: {
                    action: "add_note",
                    model_id: modelid
                },
                success: function(responce){
                    showPopup(responce);
                }
            });
        })
        //Отправка формы комментария платежа
        .on("click", ".popop_payment_note_submit", function(e){
            e.preventDefault();
            loaderOn();
            var userid = $(this).data("userid");
            saveData("Main", loaderOff);
            refreshPaymentsTable(userid, loaderOff);
        })
        //Открытие формы пополнения баланса
        .on("click", ".btn_balance", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            getPaymentPopup(userid, root + "/balance");
        })
        //Отправка формы пополнения баланса
        .on("click", ".popop_balance_payment_submit", function(e){
            e.preventDefault();
            loaderOn();
            var form = $("#createData");
            if($(form).valid() == false)
            {
                loaderOff();
                return;
            }
            var userid = $(this).data("userid");
            var value = $(form).find("input[name=value]").val();
            var description = $(form).find("textarea[name=description]").val();
            var description2 = $(form).find("textarea[name=property_26]").val();
            var type = $(form).find("input[name=type]:checked").val();
            // savePayment(userid, value, description, description2, type, "balance", function(){});
            // refreshPaymentsTable(userid, loaderOff);

            //Если это страница со списком клиентов
            if( $("#payment_from").val() == "clients" )
            {
                savePayment(userid, value, description, description2, type, "balance", refreshUserTable);
            }
            else
            {
                savePayment(userid, value, description, description2, type, "balance", function(){});
                refreshPaymentsTable(userid, loaderOff);
            }
        })
        //Открытие формы покупки индивидуальных уроков
        .on("click", ".btn_private_lessons", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            getTarifPopup(userid, 1);
        })
        //Открытие формы покупки групповых уроков
        .on("click", ".btn_group_lessons", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            getTarifPopup(userid, 2);
        })

        /**
         * Покупка тарифа
         */
        .on("click", ".popop_buy_tarif_submit", function(e){
            e.preventDefault();
            loaderOn();
            var tarifid = $("select[name=tarif_id]").val();
            var userid = $(this).data("userid");
            buyTarif(userid, tarifid);
        })

        /**
         * Форма открытия/скрытия блока (таблицы) с существующими тарифами
         */
        .on("click", ".tarifs_show", function(e){
            e.preventDefault();

            var TarifsBlock = $(".tarifs");

            if(TarifsBlock.css("display") === "block")
            {
                TarifsBlock.hide("slow");
            }
            else
            {
                TarifsBlock.show("slow");
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
            saveData("Main", refreshPayments);
        })

        /**
         * Открытие всплывающего окна создания/редактирования платежа
         */
        .on("click", ".payment_edit", function(e){
            e.preventDefault();
            var id = $(this).data("id");
            var afterSaveAction = $(this).data("after_save_action");
            editPaymentPopup(id, afterSaveAction);
        })

        /**
         * Сохранение формы редактирования платежа
         */
        .on("click", ".popop_payment_submit", function(e){
            e.preventDefault();
            loaderOn();
            var Form = $("#createData");
            var id = Form.find("input[name=id]").val();
            var value = Form.find("input[name=summ]").val();
            var date = Form.find("input[name=date]").val();
            var description = Form.find("textarea[name=description]").val();
            var afterSaveAction = Form.find("input[name=after_save_action]").val();

            $.ajax({
                type: "GET",
                url: root + "/balance",
                data: {
                    action: "payment_save",
                    id: id,
                    value: value,
                    date: date,
                    description: description
                },
                success: function(responce){
                    closePopup();

                    /**
                     * Сохранение изменений свойств платежа может происходить из разных разделов и требуют
                     * различных действия для обновления контента страницы.
                     * На данный момент информация о платеже редактируется из разделов клиента и страницы расписания преподавателя
                     */
                    switch (afterSaveAction)
                    {
                        case 'client':  //обновление контента страницы клиента
                            $(".users").empty();
                            $(".users").html(responce);
                            loaderOff();
                            break;
                        case 'teacher': //обновление контента страницы преподавателя
                            refreshSchedule();
                            break;
                        default: loaderOff();
                    }
                }
            });
        })
        .on("click", ".payment_delete", function(e){
            e.preventDefault();
            loaderOn();
            var id = $(this).data("id");

            $.ajax({
                type: "GET",
                url: root + "/balance",
                data: {
                    action: "payment_delete",
                    id: id
                },
                success: function(responce){
                    $(".users").html(responce);
                    closePopup();
                    loaderOff();
                }
            });
        });
});


function editPaymentPopup(id, afterSaveAction) {
    $.ajax({
        type: "GET",
        url: root + "/balance",
        data: {
            action: "edit_payment",
            id: id,
            afterSaveAction: afterSaveAction
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function editTarifPopup(tarifid) {
    $.ajax({
        type: "GET",
        url: "finances",
        data: {
            action: "edit_tarif_popup",
            tarifid: tarifid
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}


function refreshPayments() {
    $.ajax({
        type: "GET",
        url: root + "/finances",
        data: {
            action: "show",
        },
        success: function( responce ) {
            var isTarifsTableDisplay = $('.tarifs').css('display');
            $(".finances").html(responce);
            $('.tarifs').css('display', isTarifsTableDisplay);
            loaderOff();
        }
    });
}


function refreshPaymentsTable(userid, func) {
    $.ajax({
        type: "GET",
        url: root + "/balance",
        async: false,
        data: {
            action: "refreshTablePayments",
            user_id: userid,
            userid: userid
        },
        success: function(responce) {
            $(".users").empty();
            $(".users").append(responce);
            $("#sortingTable").tablesorter();
            func();
        }
    });
}


function getTarifPopup(id, type) {
    $.ajax({
        type: "GET",
        url: root + "/balance",
        data: {
            action: "getTarifPopup",
            type: type,
            userid: id
        },
        success: function(responce) {
            showPopup(responce);
        }
    });
}


function buyTarif(userid, tarifid)
{
    $.ajax({
        type: "GET",
        url: root + "/balance",
        async: false,
        data: {
            action: "buyTarif",
            userid: userid,
            tarifid: tarifid
        },
        success: function(responce) {
            if(responce != "")  alert(responce);
            refreshPaymentsTable(userid, loaderOff);
            closePopup();
        }
    });
}