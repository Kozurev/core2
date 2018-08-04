$(function(){
    $("body")
        .on("click", ".finances_show", function(e){
            loaderOn();
            var dateFrom = $("input[name=date_from]").val();
            var dateTo = $("input[name=date_to]").val();
            showFinancesHistory(dateFrom, dateTo);
        })
        .on("click", ".finances_payment", function(){
            getCustomPaymentPopup();
        })
        .on("click", ".popop_custom_payment_submit", function(e){
            e.preventDefault();
            loaderOn();
            var summ = $("#createData").find("input[name=summ]").val();
            var note = $("#createData").find("textarea[name=note]").val();
            saveCustomPayment(summ, note);
        });
});


function saveCustomPayment(summ, note) {
    closePopup();

    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "saveCustomPayment",
            summ: summ,
            note: note
        },
        success: function(responce){
            $(".finances_show").click();
        }
    });
}


function showFinancesHistory(periodFrom, periodTo) {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "show",
            date_from: periodFrom,
            date_to: periodTo
        },
        success: function(responce){
            $(".finances").empty();
            $(".finances").append(responce);
            loaderOff();
        }
    });
}


function getCustomPaymentPopup() {
    var popupData = "" +
        "<form name=\"createData\" id=\"createData\" action=\".\" novalidate=\"novalidate\">" +
            "<div class=\"column\"><span>Сумма</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><input type=\"number\" required name=\"summ\" class=\"form-control\"></div>" +
            "<div class=\"column\"><span>Примечание</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><textarea required name=\"note\" class=\"form-control\"></textarea></div>" +
            "<button class=\"popop_custom_payment_submit btn btn-default\">Сохранить</button>" +
        "</form>";

    showPopup(popupData);
}