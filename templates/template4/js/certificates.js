$(function(){
    $("body")
        .on("click", ".certificate_create", function(e){
            e.preventDefault();
            addNewCertificatePopup();
        })
        .on("click", ".popop_certificate_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("admin?menuTab=Main&menuAction=updateAction&ajax=1", refreshCertificatesTable);
        })
        .on("click", ".certificate_delete", function(e){
            e.preventDefault();
            loaderOn();
            var id = $(this).data("id");
            deleteItem("Certificate", id, "admin?menuTab=Main&menuAction=deleteAction&ajax=1", refreshCertificatesTable);
        });
});


function addNewCertificatePopup() {
    var popupData = "" +
        "<form name=\"createData\" id=\"createData\" action=\".\" novalidate=\"novalidate\">" +
            "<div class=\"column\"><span>Дата продажи</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><input type=\"date\" required name=\"sellDate\" class=\"form-control\"></div>" +
            "<div class=\"column\"><span>Действителен до</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><input type=\"date\" required name=\"activeTo\" class=\"form-control\"></div>" +
            "<div class=\"column\"><span>Номер</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><input type=\"text\" required name=\"number\" class=\"form-control\"></div>" +
            "<div class=\"column\"><span>Примечание</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><textarea required name=\"note\" class=\"form-control\"></textarea></div>" +
            "<input type='hidden' name='id' value=''>" +
            "<input type='hidden' name='modelName' value='Certificate'>" +
            "<button class=\"popop_certificate_submit btn btn-default\">Сохранить</button>" +
        "</form>";

    showPopup(popupData);
}


function refreshCertificatesTable() {
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "refreshCertificatesTable"
        },
        success: function(responce){
            $(".page").empty();
            $(".page").append(responce);
            loaderOff();
        }
    });
}