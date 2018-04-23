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
            saveData("admin");
            refreshPaymentsTable(userid);
        });
});


function refreshPaymentsTable(userid) {
    $.ajax({
        type: "GET",
        url: "balance",
        data: {
            action: "refreshTablePayments",
            user_id: userid
        },
        success: function(responce) {
            $("#sortingTable").remove();
            $(".page").append(responce);
            $("#sortingTable").tablesorter();
            loaderOff();
        }
    });
}