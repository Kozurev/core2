$(function(){
    $("body")
        //Открытие формы добавления комментария к платежу
        .on("click", ".payment_add_note", function(){
            var modelid = $(this).data("modelid");
            $.ajax({
                type: "GET",
                url: "balance",
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
            saveData("../admin?menuTab=Main&menuAction=updateAction&ajax=1", loaderOff);
            refreshPaymentsTable(userid, loaderOff);
        })
        //Открытие формы пополнения баланса
        .on("click", ".btn_balance", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            getPaymentPopup(userid, "balance");
        })
        //Отправка формы пополнения баланся
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
            var type = $(form).find("input[name=type]:checked").val();
            savePayment(userid, value, description, type, "balance", function(){});
            refreshPaymentsTable(userid, loaderOff);
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
        .on("click", ".popop_buy_tarif_submit", function(e){
            e.preventDefault();
            loaderOn();
            var tarifid = $("select[name=tarif_id]").val();
            var userid = $(this).data("userid");
            buyTarif(userid, tarifid);
        });
});


function refreshPaymentsTable(userid, func) {
    $.ajax({
        type: "GET",
        url: "balance",
        async: false,
        data: {
            action: "refreshTablePayments",
            user_id: userid,
            userid: userid
        },
        success: function(responce) {
            $(".page").empty();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            func();
        }
    });
}


function getTarifPopup(id, type) {
    $.ajax({
        type: "GET",
        url: "balance",
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
        url: "balance",
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