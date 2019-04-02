$(function(){
    $("body")
        //Форма добавления нового сертификата
        // .on("click", ".certificate_edit", function(e){
        //     e.preventDefault();
        //     var id = $(this).data("id");
        //     editCertificatePopup(id);
        // })
        //Сохранение нового сертификата
        .on("click", ".popop_certificate_submit", function(e){
            e.preventDefault();
            loaderOn();
            var formData = $("#createData").serialize();
            certificateSave(formData, refreshCertificatesTable);
        })
        //Форма добавления комментария сертификата
        // .on("click", ".add_comment", function(e){
        //     e.preventDefault();
        //     var certificate_id = $(this).data("cert-id");
        //     addNewCertificateNotePopup(certificate_id);
        // })
        //Сохранение комментария сертификата
        .on("click", ".popop_certificate_note_submit", function(e){
            e.preventDefault();
            loaderOn();
            var formData = $("#createData").serialize();
            closePopup();
            saveCertificateComment(formData, refreshCertificatesTable);
        });
        // .on("click", ".certificate_delete", function(e){
        //     e.preventDefault();
        //     loaderOn();
        //     var id = $(this).data("id");
        //     deleteItem("Certificate", id, refreshCertificatesTable);
        // });
});


function editCertificatePopup(id) {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'edit_popup',
            id: id
        },
        success: function(response) {
            showPopup(response);
        }
    });
}


function addNewCertificateNotePopup(certificateId) {
    var popupData = "" +
        "<form name=\"createData\" id=\"createData\" action=\".\" novalidate=\"novalidate\">" +
            "<div class=\"column\"><span>Текст комментария</span><span style=\"color:red\">*</span></div>" +
            "<div class=\"column\"><input type=\"text\" required name=\"note\" class=\"form-control\"></div>" +
            "<input type=\"hidden\" name=\"certificate_id\" value=\"" + certificateId + "\" >" +
            "<button class=\"popop_certificate_note_submit btn btn-default\">Сохранить</button>" +
        "</form>";
    showPopup(popupData);
}


function certificateSave(formData) {
    $.ajax({
        type: 'GET',
        url: '?action=saveCertificate',
        data: formData,
        success: function(response){
            if (response != '') {
                notificationError('Ошибка: ' + response);
            }
            closePopup();
            refreshCertificatesTable();
        }
    });
}


function saveCertificateComment(formData, func) {
    $.ajax({
        type: 'GET',
        url: '?action=saveCertificateNote',
        data: formData,
        success: function(response){
            if (responce != '') {
                notificationError('Ошибка: ' + response);
            }
            func();
        }
    });
}


function refreshCertificatesTable() {
    $.ajax({
        type: 'GET',
        url: '',
        data: {
            action: 'refreshCertificatesTable'
        },
        success: function(response){
            $('.page').html(response);
            loaderOff();
        }
    });
}