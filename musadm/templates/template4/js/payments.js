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
            saveData("Main", function(response){loaderOff();});
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

        .on("click", ".finances_payment_rate_config", function(e){
            e.preventDefault();

            var teacherRateConfigBlock = $(".teacher_rate_config_block");

            if(teacherRateConfigBlock.css("display") == "none")
            {
                teacherRateConfigBlock.show("slow");
            }
            else
            {
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
        })

        /**
         * Открытие всплывающего окна создания/редактирования платежа
         */
        .on("click", ".payment_edit", function(e){
            e.preventDefault();
            var id = $(this).data("id");
            var afterSaveAction = $(this).data("after_save_action");

            if(afterSaveAction == "payment" && $(this).data("type") < 3)
            {
                if(confirm("Редактирование суммы данного платежа не повлияет на баланс клиента. Вы хотите продолжить?"))
                {
                    editPaymentPopup(id, afterSaveAction);
                }
            }
            else
            {
                editPaymentPopup(id, afterSaveAction);
            }
        })

        /**
         * Сохранение формы редактирования платежа
         */
        .on("click", ".popop_payment_submit", function(e){
            e.preventDefault();
            loaderOn();

            var afterSaveAction = $("#createData").find("input[name=after_save_action]").val();

            saveData("Main", function(response){
                /**
                 * Сохранение изменений свойств платежа может происходить из разных разделов и требуют
                 * различных действия для обновления контента страницы.
                 * На данный момент информация о платеже редактируется из разделов клиента и страницы расписания преподавателя
                 */
                switch (afterSaveAction)
                {
                    case 'client':  //обновление контента страницы клиента
                        refreshUserTable();
                        break;
                    case 'teacher': //обновление контента страницы преподавателя
                        refreshSchedule();
                        break;
                    case 'payment': //обновление контента страницы финансов
                        refreshPayments();
                        break;
                    default: loaderOff();
                }
            });
        })
        .on("click", ".teacher_payment_delete", function(e){
            e.preventDefault();
            loaderOn();
            var id = $(this).data("id");
            var paymentValue = Number( $(this).parent().parent().parent().find(".value").text() );

            var debt = $("#teacher-debt");
            var alreadyPayed = $("#teacher-payed");

            var debtVal = Number(debt.text());
            var alreadyPayedVal = Number(alreadyPayed.text());

            debt.text(debtVal + paymentValue);
            alreadyPayed.text(alreadyPayedVal - paymentValue);

            $(this).parent().parent().parent().remove();

            deletePayment(id, function(response){
                loaderOff();
            });
        });
});


/**
 * Функция удаления платежа
 *
 * @date 21.01.2019 09:50
 * @param paymentId
 * @param func
 */
function deletePayment(paymentId, func) {
    $.ajax({
        type: "GET",
        url: root + "/balance",
        data: {
            action: "payment_delete",
            id: paymentId
        },
        success: function(response) {
            func(response);
        }
    });
}


function editPaymentPopup(id, afterSaveAction) {
    $.ajax({
        type: "GET",
        url: root + "/finances",
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


/**
 * Открытие всплывающего окна создания / редактирования тарифа
 *
 * @param tarifid
 */
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