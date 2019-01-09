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
        })
        .on("click", ".edit_rate", function(e){
            e.preventDefault();

            $(this).css("display", "none");
            $(this).parent().find(".save_rate").css("display", "inline-block");

            var valueSpan = $(this).parent().parent().find(".current_value");
            var valueInput = $(this).parent().parent().find(".edit_rate_value");

            var oldValue = valueSpan.text();
            valueSpan.css("display", "none");

            valueInput.val(oldValue);
            valueInput.css("display", "inline-block");
        })
        .on("click", ".save_rate", function(e){
            e.preventDefault();

            $(this).css("display", "none");
            $(this).parent().find(".edit_rate").css("display", "inline-block");

            var valueSpan = $(this).parent().parent().find(".current_value");
            var valueInput = $(this).parent().parent().find(".edit_rate_value");

            var newValue = valueInput.val();
            valueInput.css("display", "none");

            valueSpan.text(newValue);
            valueSpan.css("display", "inline-block");

            var propertyTagName = valueInput.data("prop-name");
            var propertyValue = valueInput.val();
            var directorId = $("#director_id").val();

            savePropertyValue( propertyTagName, propertyValue, "User", directorId );
        })
        .on("change", ".is_default_rate", function(){
            var propertyValue;
            $(this).is(":checked")
                ?   propertyValue = 0
                :   propertyValue = 1;

            var propertyTagName = $(this).data( "prop-name" );
            var teacherId = $("#teacher_id").val();

            savePropertyValue( propertyTagName, propertyValue, "User", teacherId );
        })
        .on("click", ".is_default_rate_director", function(e){
            var propertyValue;
            $(this).is(":checked")
                ?   propertyValue = 1
                :   propertyValue = 0;

            var propertyTagName = $(this).data( "prop-name" );
            var teacherId = $("#director_id").val();

            savePropertyValue( propertyTagName, propertyValue, "User", teacherId );
        })
        .on("click", ".teacher_rate_edit", function(e){
            e.preventDefault();

            $(this).css("display", "none");
            $(this).parent().find(".teacher_rate_save").css("display", "inline-block");

            var valueSpan = $(this).parent().parent().find(".indiv-rate");
            var valueInput = $(this).parent().parent().find(".edit_rate_value");

            var oldValue = valueSpan.text();
            valueSpan.css("display", "none");

            valueInput.val(oldValue);
            valueInput.css("display", "inline-block");
        })
        .on("click", ".teacher_rate_save", function(e){
            e.preventDefault();

            $(this).css("display", "none");
            $(this).parent().find(".teacher_rate_edit").css("display", "inline-block");

            var valueSpan = $(this).parent().parent().find(".indiv-rate");
            var valueInput = $(this).parent().parent().find(".edit_rate_value");

            var newValue = valueInput.val();
            valueInput.css("display", "none");

            valueSpan.text(newValue);
            valueSpan.css("display", "inline-block");

            var propertyTagName = valueInput.data("prop-name");
            var propertyValue = valueInput.val();
            var teacherId = $("#teacher_id").val();

            savePropertyValue( propertyTagName, propertyValue, "User", teacherId );
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