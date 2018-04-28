$(function(){
    $("body")
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
        .on("click", ".popop_payment_note_submit", function(e){
            e.preventDefault();
            loaderOn();
            var userid = $(this).data("userid");
            saveData("admin?menuTab=Main&menuAction=updateAction&ajax=1", loaderOff);
            refreshPaymentsTable(userid, loaderOff);
        })
        .on("click", ".btn_balance", function(e){
            e.preventDefault();
            var userid = $(this).data("userid");
            getPaymentPopup(userid, "balance");
        })
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
            savePayment(userid, value, description, type, "balance", loaderOn);
            refreshPaymentsTable(userid, loaderOff);
            //refreshBalanceTable(userid, loaderOff);
        });
});


function refreshPaymentsTable(userid, func) {
    $.ajax({
        type: "GET",
        url: "balance",
        data: {
            action: "refreshTablePayments",
            user_id: userid,
            userid: userid
        },
        success: function(responce) {
            // $("#sortingTable").remove();
            // $(".page").append(responce);
            // $("#sortingTable").tablesorter();
            // func();
            $(".page").empty();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            func();
        }
    });
}