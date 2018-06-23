$(function(){
    $("body")
        .on("click", ".applocationPopup", function(e){
            e.preventDefault();
            $("#ekkoLightbox-1").show("slow");
        })
        .on("click", ".application_submit", function(e){
            e.preventDefault();
            var Form = $("form[name=application]");
            if(!Form.valid()) return;
            loaderOn();
            applicationSave(Form);
        })
        .on("click", "#mark_as_done", function(e){
            e.preventDefault();
            loaderOn();
            var app_id = $("#application_id").val();
            markAs("done");
        })
        .on("click", "#mark_as_in_process", function(e){
            e.preventDefault();
            loaderOn();
            var app_id = $("#application_id").val();
            markAs("in_process");
        })
        .on("click", "#app_save", function(e){
            e.preventDefault();
            var formData = $("#app_form").serialize();
            loaderOn();
            saveApp(formData);
        })
        .on("click", "#app_delete", function(e){
            e.preventDefault();
            loaderOn();
            deleteApp();
        })
        .on("click", "#add_comment", function(e){
            e.preventDefault();
            var Form = $("#comment-form");
            if(!Form.valid())   return false;
            loaderOn();
            saveAppComment(Form);
        });
});


function saveAppComment(Form) {
    var formData = Form.serialize();
    var text = Form.find("textarea").val();
    formData += "&action=save_comment";

    $.ajax({
        type: "GET",
        url: "",
        data: formData,
        success: function(responce){
            if(responce.length != 19)
            {
                showError(responce);
                loaderOff();
            }
            var Forum = $(".forum");
            var forumTitle = Forum.find(".title").clone();
            var username = $(".current-user").text();
            var datetime = responce;
            Forum.find(".title").remove();
            var forumData = Forum.html();
            Forum.empty();
            Forum.append(forumTitle);

            var newMessage = "<div class=\"message\">\n" +
        "                        <div class=\"head\">\n" +
        "                            <div class=\"username\">"+username+"</div>\n" +
        "                            <div class=\"datetime\">"+datetime+"</div>\n" +
        "                        </div>\n" +
        "\n" +
        "                        <div class=\"body\">\n" + text +
        "                        </div>\n" +
        "                    </div>";

            Forum.append(newMessage);
            Forum.append(forumData);

            // $(".page-wrapper").empty();
            // $(".page-wrapper").html(responce);
            showMessage("Сохранено", "Ваш комментарий был сохранен.", "default");

            loaderOff();
        }
    });
}


function deleteApp() {
    $.ajax({
        type: "GET",
        url: "",
        data: { action: "delete" },
        success: function(responce){
            if(responce != "") showError(responce);
            else history.back();
        }
    });
}


function saveApp(data) {
    data += "&action=save";
    $.ajax({
        type: "GET",
        url: "",
        data: data,
        success: function(responce){
            if(responce != "") showError(responce);
            else showMessage("Данные успешно сохранены", "", "default");
            loaderOff();
        }
    });
}


function markAs( marker ) {
    $.ajax({
        type: "GET",
        url: "",
        data: { action: marker },
        success: function(responce){
            //if(responce != "1") showError(responce);
            $(".page-wrapper").empty();
            $(".page-wrapper").html(responce);
            loaderOff();
        }
    });
}


function applicationSave(Form) {
    formData = new FormData(Form.get(0));

    $.ajax({
        type: "POST",
        url: "application/save",
        contentType: false, // важно - убираем форматирование данных по умолчанию
        processData: false, // важно - убираем преобразование строк по умолчанию
        data: formData,
        success: function(responce){
            if(responce == "1")
            {
                showMessage("Отправлено!", "Ваша заявка принята и будет рассмотренав ближайшее время", "default");
            }
            else
            {
                showError(responce);
            }

            $("#ekkoLightbox-1").hide("slow");
            loaderOff();
        }
    });
}


function uploadFiles(app_id) {
    var $input = $("#images");
    var fd = new FormData;
    fd.append('img', $input.prop('files'));
    fd.append('app_id', app_id);

    $.ajax({
        type: "POST",
        url: "application/upload",
        processData: false,
        contentType: false,
        data: fd,
        success: function(responce){
            alert(responce);
            loaderOff();
            closePopup();
        }
    });
}