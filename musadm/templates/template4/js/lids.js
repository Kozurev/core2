$(function(){
    $(document)
        .on("click", ".lid_submit", function(e){
            e.preventDefault();
            loaderOn();
            var data = "";
            data += "surname="+$("input[name=surname]").val();
            data += "&comment="+$("input[name=comment]").val();
            data += "&name="+$("input[name=name]").val();
            data += "&number="+$("input[name=number]").val();
            data += "&vk="+$("input[name=vk]").val();
            data += "&control_date="+$("input[name=control_date]").val();
            data += "&source="+$("input[name=source]").val();
            saveLid(data, refreshLidTable);
        })
        .on("click", ".add_lid_comment", function(e){
            e.preventDefault();
            var lidid = $(this).data("lidid");
            getCommentPopup(lidid);
        })
        .on("click", ".popop_lid_comment_submit", function(e){
            e.preventDefault();
            loaderOn();
            saveData("Main", refreshLidTable);
        })
        .on("change", ".lid_status", function(){
            loaderOn();
            var lidid = $(this).data("lidid");
            var statusid = $(this).val();
            changeStatus(lidid, statusid, refreshLidTable);
        })
        .on("change", ".lid_date", function(){
            loaderOn();
            var lidid = $(this).data("lidid");
            var date = $(this).val();
            changeDate(lidid, date, loaderOff);
        })
        .on("click", ".lids_show", function(){
            loaderOn();
            refreshLidTable();
        });
});

function refreshLidTable() {

    var dateFrom = $("input[name=date_from]").val();
    var dateTo = $("input[name=date_to]").val();

    $.ajax({
        type: "GET",
        url: "",
        async: false,
        data: {
            action: "refreshLidTable",
            date_from: dateFrom,
            date_to: dateTo
        },
        success: function(responce){
            $(".lids").empty();
            $(".lids").append(responce);
            loaderOff();
        }
    });
}

function getCommentPopup(lidid){
    $.ajax({
        type: "GET",
        url: "lids",
        data: {
            action: "add_note_popup",
            model_id: lidid
        },
        success: function(responce){
            showPopup(responce);
        }
    });
}


function saveLid(data, func){
    $.ajax({
        type: "GET",
        url: "lids?action=save_lid",
        async: false,
        data: data,
        success: function(responce){
            func();
        }
    });
}

function changeStatus(lidid, statusid, func){
    $.ajax({
        type: "GET",
        url: "",
        data: {
            action: "changeStatus",
            model_id: lidid,
            status_id: statusid
        },
        success: function(responce){
            func();
        }
    });
}

function changeDate(lidid, date, func){
    $.ajax({
        type: "GET",
        url: "lids",
        async: false,
        data: {
            action: "changeDate",
            model_id: lidid,
            date: date
        },
        success: function(responce){
            func();
        }
    });
}