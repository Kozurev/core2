$(function(){
    $("body")
        .on("click", ".finances_show", function(e){
            loaderOn();
            var dateFrom = $("input[name=date_from]").val();
            var dateTo = $("input[name=date_to]").val();
            showFinancesHistory(dateFrom, dateTo);
        })
        .on("click", ".finances_payment", function(){
            editPaymentPopup(0, "payment");
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
        //Сохранение значения одной из ставок преподавателей
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
        })
        //Открытие всплывающего окна добавления/удаления типов платежей
        .on("click", ".finances_payment_types", function(e){
            e.preventDefault();
            showFinancesTypes();
        })
        //Сохранение нового типа платежа
        .on("click", ".finances_payment_type_append", function(e){
            e.preventDefault();
            var newTypeName = $("#input_new_payment_type").val();

            if(newTypeName.length == 0)
            {
                $("#input_new_payment_type").addClass("error");
                $("label[for=input_new_payment_type]").addClass("error");
                return false;
            }

            //loaderOn();
            savePaymentType(0, newTypeName, function(response){
                $(".finances_payment_type_list").append(response);
                $("#input_new_payment_type").val("");
                //loaderOff();
            });
        })
        //Удаление типа(ов) платежа(эй)
        .on("click", ".finances_payment_type_delete", function(e){
            e.preventDefault();
            var deletingTypes = $(".finances_payment_type_list").find("option:selected");

            var deletingTypesIds = [];
            $.each(deletingTypes, function(key, option){
                deletingTypesIds.push($(option).val());
            });

            deletePaymentTypes(deletingTypesIds, function(response){
                var options = $(".finances_payment_type_list").find("option:selected");
                $.each(options, function(key, option){
                    console.log(option);
                    $(option).remove();
                });
            });
        });


});


// function saveCustomPayment(summ, note) {
//     closePopup();
//
//     $.ajax({
//         type: "GET",
//         url: "",
//         data: {
//             action: "saveCustomPayment",
//             summ: summ,
//             note: note
//         },
//         success: function(responce){
//             $(".finances_show").click();
//         }
//     });
// }


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


/**
 * Открытие всплывающего окна с созданием/удалением типов платежей
 */
function showFinancesTypes() {
    $.ajax({
        type: "GET",
        url: root + "/finances",
        data: {
            action: "getPaymentTypesPopup"
        },
        success: function(response) {
            loaderOff();
            showPopup( response );
        }
    });
}


/**
 * Сохранение типа платежа
 *
 * @date 20.10.2019 16:20
 *
 * @param id - id сохраняемого типа
 * @param title - название
 * @param func - обработчик после выполнения AJAX-запроса
 */
function savePaymentType(id, title, func) {
    $.ajax({
        type: "GET",
        url: root + "/finances",
        data: {
            action: "savePaymentType",
            id: id,
            title: title
        },
        success: function(response) {
            func(response);
        }
    });
}


/**
 * Удаление типа/типов платежей по id
 *
 * @date 20.01.2019 17:50
 *
 * @param ids - массив идентификаторов платежей
 * @param func - обработчик после выполнения AJAX-запроса
 */
function deletePaymentTypes(ids, func) {
    $.ajax({
        type: "GET",
        url: root + "/finances",
        data: {
            action: "deletePaymentTypes",
            ids: ids
        },
        success: function(response) {
            func(response);
        }
    });
}


function getCustomPaymentPopup() {
    loaderOn();

    $.ajax({
        type: "GET",
        url: root + "/finances",
        data: {
            action: "getCustomPaymentPopup"
        },
        success: function(response) {
            loaderOff();
            showPopup(response);
        }
    });
}